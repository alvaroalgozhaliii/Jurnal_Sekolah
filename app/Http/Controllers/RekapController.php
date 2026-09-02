<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanIzin;
use App\Models\JurnalHarian;
use App\Models\Guru;
use App\Models\Jadwal;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RekapController extends Controller
{
    /**
     * Rekap Persetujuan Izin per Bulan dalam format Excel (.xlsx)
     */
    public function rekapIzinExcel(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        
        $monthsIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanNama = $monthsIndo[$bulan] ?? 'Bulan ' . $bulan;
        $periodeLabel = $bulanNama . ' ' . $tahun;

        $izinSiswa = PengajuanIzin::with(['siswa.kelas', 'wakaApprover', 'satpam', 'pengaju'])
            ->where(function($q) {
                $q->where('kategori', 'like', '%siswa%')
                  ->orWhereNotNull('id_siswa');
            })
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $izinGuru = PengajuanIzin::with(['guru', 'wakaApprover', 'pengaju'])
            ->where(function($q) {
                $q->where('kategori', 'like', '%guru%')
                  ->orWhereNotNull('id_guru');
            })
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle("Rekap Persetujuan Izin - $periodeLabel")
            ->setCreator('Jurnal Sekolah SMKN 1 Boyolangu');

        // Sheet 1: Izin Siswa
        $s1 = $spreadsheet->getActiveSheet();
        $s1->setTitle('Izin Siswa');
        $this->buildTitleHeader($s1, "REKAP PERSETUJUAN IZIN SISWA — " . strtoupper($periodeLabel), 'I');
        
        $headers1 = ['No', 'Tanggal', 'Nama Siswa', 'Kelas', 'Kategori / Jenis', 'Keterangan / Alasan', 'Status Persetujuan', 'Disetujui Waka / Piket', 'Verifikasi Satpam'];
        $this->writeHeaderRow($s1, $headers1, 2);

        $row1 = 3;
        foreach ($izinSiswa as $idx => $iz) {
            $statusText = match($iz->status) {
                'approved', 'disetujui', 'disetujui_waka' => 'Disetujui',
                'pending_waka', 'pending' => 'Menunggu Waka',
                'pending_piket' => 'Menunggu Piket',
                'pending_satpam' => 'Menunggu Satpam',
                'verified', 'completed' => 'Selesai / Terverifikasi',
                'rejected', 'ditolak_waka', 'ditolak_satpam' => 'Ditolak',
                default => ucfirst(str_replace('_', ' ', $iz->status ?? '-'))
            };

            $satpamStatus = $iz->satpam ? ($iz->satpam->nama . ' (Pkl ' . ($iz->waktu_keluar_satpam ? date('H:i', strtotime($iz->waktu_keluar_satpam)) : '-') . ')') : ($iz->butuh_satpam ? 'Belum Lewat Pos' : 'Tidak Perlu');

            $s1->setCellValue("A{$row1}", $idx + 1);
            $s1->setCellValue("B{$row1}", $iz->created_at ? $iz->created_at->format('d/m/Y H:i') : '-');
            $s1->setCellValue("C{$row1}", $iz->siswa?->nama ?? '-');
            $s1->setCellValue("D{$row1}", $iz->siswa?->kelas?->nama_kelas ?? '-');
            $s1->setCellValue("E{$row1}", ucfirst(str_replace('_', ' ', $iz->kategori ?? $iz->jenis_izin ?? 'Izin Siswa')));
            $s1->setCellValue("F{$row1}", $iz->keterangan ?? $iz->alasan ?? '-');
            $s1->setCellValue("G{$row1}", $statusText);
            $s1->setCellValue("H{$row1}", $iz->wakaApprover?->nama ?? '-');
            $s1->setCellValue("I{$row1}", $satpamStatus);
            $row1++;
        }

        if ($row1 > 3) {
            $s1->getStyle("A2:I" . ($row1 - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        foreach (range('A', 'I') as $col) {
            $s1->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Izin Guru
        $s2 = $spreadsheet->createSheet();
        $s2->setTitle('Izin Guru');
        $this->buildTitleHeader($s2, "REKAP PERSETUJUAN IZIN GURU — " . strtoupper($periodeLabel), 'H');

        $headers2 = ['No', 'Tanggal', 'Nama Guru', 'NIP', 'Kategori Izin', 'Keterangan', 'Status Persetujuan', 'Disetujui Waka'];
        $this->writeHeaderRow($s2, $headers2, 2);

        $row2 = 3;
        foreach ($izinGuru as $idx => $iz) {
            $statusText = match($iz->status) {
                'approved', 'disetujui', 'disetujui_waka' => 'Disetujui',
                'pending_waka', 'pending' => 'Menunggu Persetujuan',
                'rejected', 'ditolak_waka' => 'Ditolak',
                default => ucfirst(str_replace('_', ' ', $iz->status ?? '-'))
            };

            $s2->setCellValue("A{$row2}", $idx + 1);
            $s2->setCellValue("B{$row2}", $iz->created_at ? $iz->created_at->format('d/m/Y H:i') : '-');
            $s2->setCellValue("C{$row2}", $iz->guru?->nama ?? ($iz->pengaju?->nama ?? '-'));
            $s2->setCellValue("D{$row2}", $iz->guru?->nip ?? '-');
            $s2->setCellValue("E{$row2}", ucfirst(str_replace('_', ' ', $iz->kategori ?? 'Izin Guru')));
            $s2->setCellValue("F{$row2}", $iz->keterangan ?? $iz->alasan ?? '-');
            $s2->setCellValue("G{$row2}", $statusText);
            $s2->setCellValue("H{$row2}", $iz->wakaApprover?->nama ?? '-');
            $row2++;
        }

        if ($row2 > 3) {
            $s2->getStyle("A2:H" . ($row2 - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        foreach (range('A', 'H') as $col) {
            $s2->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 3: Ringkasan Bulanan
        $s3 = $spreadsheet->createSheet();
        $s3->setTitle('Ringkasan Total');
        $this->buildTitleHeader($s3, "RINGKASAN IZIN & DISPENSASI — " . strtoupper($periodeLabel), 'C');
        $this->writeHeaderRow($s3, ['No', 'Kategori Pengajuan', 'Jumlah Pengajuan'], 2);
        
        $s3->setCellValue('A3', 1); $s3->setCellValue('B3', 'Total Pengajuan Izin Siswa'); $s3->setCellValue('C3', $izinSiswa->count());
        $s3->setCellValue('A4', 2); $s3->setCellValue('B4', 'Total Pengajuan Izin Guru'); $s3->setCellValue('C4', $izinGuru->count());
        $s3->setCellValue('A5', 3); $s3->setCellValue('B5', 'Total Pengajuan Selesai / Disetujui'); $s3->setCellValue('C5', $izinSiswa->whereIn('status', ['approved', 'disetujui', 'disetujui_waka', 'verified', 'completed'])->count() + $izinGuru->whereIn('status', ['approved', 'disetujui', 'disetujui_waka', 'verified', 'completed'])->count());
        $s3->setCellValue('A6', 4); $s3->setCellValue('B6', 'Total Pengajuan Ditolak'); $s3->setCellValue('C6', $izinSiswa->whereIn('status', ['rejected', 'ditolak_waka', 'ditolak_satpam'])->count() + $izinGuru->whereIn('status', ['rejected', 'ditolak_waka'])->count());
        $s3->setCellValue('A7', 'TOTAL'); $s3->setCellValue('B7', 'GRAND TOTAL PENGAJUAN BULAN INI'); $s3->setCellValue('C7', $izinSiswa->count() + $izinGuru->count());

        $s3->getStyle('A2:C7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $s3->getStyle('A7:C7')->getFont()->setBold(true);
        foreach (['A', 'B', 'C'] as $col) {
            $s3->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Rekap_Izin_{$bulanNama}_{$tahun}.xlsx";

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Rekap Jurnal Harian KBM per Bulan dalam format Excel (.xlsx)
     * Hanya mencakup guru-guru yang memiliki jadwal mengajar aktif
     */
    public function rekapJurnalExcel(Request $request)
    {
        $user = Auth::user();
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $monthsIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanNama = $monthsIndo[$bulan] ?? 'Bulan ' . $bulan;
        $periodeLabel = $bulanNama . ' ' . $tahun;

        // Query Jurnal Harian: hanya untuk guru yang memiliki jadwal mengajar aktif
        $query = JurnalHarian::with(['guru', 'jadwal.kelas'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereHas('guru.jadwal', function($q) {
                $q->where('aktif', 1);
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('id_guru', 'asc');

        if ($user->isGuru() && !$user->isAdmin()) {
            $idGuru = $user->guru?->id_guru ?? 0;
            $query->where('id_guru', $idGuru);
        }

        $jurnals = $query->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle("Rekap Jurnal KBM - $periodeLabel")
            ->setCreator('Jurnal Sekolah SMKN 1 Boyolangu');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jurnal KBM');

        $this->buildTitleHeader($sheet, "REKAP JURNAL HARIAN KBM MENGAJAR GURU — " . strtoupper($periodeLabel), 'J');

        $headers = ['No', 'Tanggal', 'Nama Guru', 'Kelas', 'Hari', 'Jam Ke', 'Mata Pelajaran', 'Materi Pokok', 'Sub Materi / Catatan', 'Status Keterlaksanaan'];
        $this->writeHeaderRow($sheet, $headers, 2);

        $row = 3;
        foreach ($jurnals as $idx => $j) {
            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $j->tanggal ? Carbon::parse($j->tanggal)->format('d/m/Y') : '-');
            $sheet->setCellValue("C{$row}", $j->guru?->nama ?? '-');
            $sheet->setCellValue("D{$row}", $j->jadwal?->kelas?->nama_kelas ?? '-');
            $sheet->setCellValue("E{$row}", $j->jadwal?->hari ?? '-');
            $sheet->setCellValue("F{$row}", $j->jadwal?->jam_ke ?? '-');
            $sheet->setCellValue("G{$row}", $j->mapel ?? ($j->jadwal?->mapel ?? '-'));
            $sheet->setCellValue("H{$row}", $j->materi ?? '-');
            $sheet->setCellValue("I{$row}", ($j->sub_materi ?? '') . ($j->catatan_pengajaran ? ' | ' . $j->catatan_pengajaran : ''));
            $sheet->setCellValue("J{$row}", ucfirst(str_replace('_', ' ', $j->status_keterlaksanaan ?? 'Terlaksana')));
            $row++;
        }

        if ($row > 3) {
            $sheet->getStyle("A2:J" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Rekap Kehadiran Mengajar per Guru
        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('Rekap per Guru');
        $this->buildTitleHeader($sheetGuru, "REKAP JUMLAH JURNAL KBM PER GURU — " . strtoupper($periodeLabel), 'E');
        $this->writeHeaderRow($sheetGuru, ['No', 'Nama Guru', 'NIP', 'Bidang Studi', 'Total Jurnal Terisi'], 2);

        // Hanya guru yang punya jadwal mengajar
        $guruPengajar = Guru::whereHas('jadwal', fn($q) => $q->where('aktif', 1))
            ->orderBy('nama', 'asc')
            ->get();

        $rg = 3;
        foreach ($guruPengajar as $idx => $g) {
            $countJurnal = $jurnals->where('id_guru', $g->id_guru)->count();
            $sheetGuru->setCellValue("A{$rg}", $idx + 1);
            $sheetGuru->setCellValue("B{$rg}", $g->nama);
            $sheetGuru->setCellValue("C{$rg}", $g->nip ?? '-');
            $sheetGuru->setCellValue("D{$rg}", $g->bidang_studi ?? '-');
            $sheetGuru->setCellValue("E{$rg}", $countJurnal);
            $rg++;
        }

        if ($rg > 3) {
            $sheetGuru->getStyle("A2:E" . ($rg - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        foreach (range('A', 'E') as $col) {
            $sheetGuru->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Rekap_Jurnal_KBM_{$bulanNama}_{$tahun}.xlsx";

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function buildTitleHeader($sheet, string $title, string $lastCol)
    {
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);
    }

    private function writeHeaderRow($sheet, array $headers, int $rowNumber)
    {
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}{$rowNumber}", $h);
            $col++;
        }
        $lastCol = chr(64 + count($headers));
        $sheet->getStyle("A{$rowNumber}:{$lastCol}{$rowNumber}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getRowDimension($rowNumber)->setRowHeight(22);
    }
}
