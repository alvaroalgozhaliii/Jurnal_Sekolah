import subprocess
import os
import sys

RENAMES = {
    "08ae0e3": "Pengembangan Modul Data Guru (CRUD Data Guru & Detail View)",
    "a9d31e8": "Pengembangan Modul Master Data (CRUD Jurusan, Kelas, & Siswa)",
    "77d5f37": "Pengembangan Modul Manajemen Jadwal Pelajaran",
    "84b3941": "Pengembangan Modul Jurnal Harian KBM (Pengisian & Soft Delete)",
    "e1c0321": "Pengembangan Modul Absensi Siswa & Autentikasi Multi-Role",
    "ec0785c": "Restrukturisasi Sistem Peran (RBAC), Multi-Dashboard, Pengguna & Backup",
    "ef7824a": "Pengembangan Modul Kepala Sekolah, Pengajuan Izin/Dispen & WA Gateway",
    "496e4e1": "Pengembangan Fitur Approval Pengajuan Izin (Satpam / Waka / Piket)",
    "7438e5c": "Penyesuaian & Testing Skema Migrasi Database (Users, Guru, Siswa)",
    "1c49b3e": "Revisi Sistem Jadwal Pelajaran, KbmService, Presensi Piket & Dispen Guru",
}

BASE = "backup-sebelum-ganti-nama"

# Commit tambahan setelah 289a200.
# Tree dan metadata diambil dari object asli.
EXTRA = [
    "f04cf017d1c6832045d2991211f962390094faea",
    "1361f6bb433a6c31c6641ca5e488546e6de9f1db",
    "416ba666cfe139996415c8d50fb1e69d3a1127ab",
]


def git(*args, input_text=None):
    result = subprocess.run(
        ["git", *args],
        input=input_text,
        text=True,
        capture_output=True,
    )

    if result.returncode != 0:
        print(result.stderr)
        sys.exit(result.returncode)

    return result.stdout.strip()


def get_commit_data(commit):
    data = git(
        "show",
        "-s",
        "--format=%H%n%an%n%ae%n%aI%n%cn%n%ce%n%cI%n%B",
        commit,
    )

    lines = data.splitlines()

    return {
        "hash": lines[0],
        "author_name": lines[1],
        "author_email": lines[2],
        "author_date": lines[3],
        "committer_name": lines[4],
        "committer_email": lines[5],
        "committer_date": lines[6],
        "message": "\n".join(lines[7:]).rstrip(),
    }


def get_tree(commit):
    return git("rev-parse", f"{commit}^{{tree}}")


def commit_tree(tree, parent, data, message):
    env = os.environ.copy()

    env["GIT_AUTHOR_NAME"] = data["author_name"]
    env["GIT_AUTHOR_EMAIL"] = data["author_email"]
    env["GIT_AUTHOR_DATE"] = data["author_date"]

    env["GIT_COMMITTER_NAME"] = data["committer_name"]
    env["GIT_COMMITTER_EMAIL"] = data["committer_email"]
    env["GIT_COMMITTER_DATE"] = data["committer_date"]

    cmd = ["git", "commit-tree", tree]

    if parent:
        cmd += ["-p", parent]

    result = subprocess.run(
        cmd,
        input=message,
        text=True,
        capture_output=True,
        env=env,
    )

    if result.returncode != 0:
        print(result.stderr)
        sys.exit(result.returncode)

    return result.stdout.strip()


print("=== MEMBUAT HISTORY ASLI ===")
print()

print("Mengambil 56 commit dari:", BASE)

base_commits = git(
    "rev-list",
    "--reverse",
    BASE,
).splitlines()

if len(base_commits) != 56:
    print(f"ERROR: base seharusnya 56 commit, ditemukan {len(base_commits)}")
    sys.exit(1)

print(f"Base OK: {len(base_commits)} commit")

new_parent = None
mapping = []

# ---------------------------------------------------------
# 1. Bangun ulang 56 commit asli
# ---------------------------------------------------------

for index, old_hash in enumerate(base_commits, start=1):
    data = get_commit_data(old_hash)
    tree = get_tree(old_hash)

    short = old_hash[:7]

    if short in RENAMES:
        message = RENAMES[short]
        print(f"[{index}/59] {short} -> {message}")
    else:
        message = data["message"]
        print(f"[{index}/59] {short} -> {message}")

    new_hash = commit_tree(
        tree,
        new_parent,
        data,
        message,
    )

    mapping.append((old_hash, new_hash))
    new_parent = new_hash


# ---------------------------------------------------------
# 2. Tambahkan 3 commit setelah 289a200
# ---------------------------------------------------------

for number, old_hash in enumerate(EXTRA, start=57):
    data = get_commit_data(old_hash)
    tree = get_tree(old_hash)

    print(f"[{number}/59] {old_hash[:7]} -> {data['message']}")

    new_hash = commit_tree(
        tree,
        new_parent,
        data,
        data["message"],
    )

    mapping.append((old_hash, new_hash))
    new_parent = new_hash


# ---------------------------------------------------------
# 3. Validasi
# ---------------------------------------------------------

print()
print("=== VALIDASI ===")

if len(mapping) != 59:
    print(f"ERROR: hasil seharusnya 59 commit, ditemukan {len(mapping)}")
    sys.exit(1)

final_new = new_parent

print(f"Jumlah commit : {len(mapping)}")
print(f"Commit akhir  : {final_new}")
print()

# Simpan hasil mapping
with open("mapping_history_asli.txt", "w", encoding="utf-8") as f:
    for old, new in mapping:
        f.write(f"{old} -> {new}\n")

# Buat branch baru
branch_name = "history-asli"

# Jika branch sudah ada, hapus hanya branch tersebut.
existing = subprocess.run(
    ["git", "rev-parse", "--verify", branch_name],
    capture_output=True,
    text=True,
)

if existing.returncode == 0:
    subprocess.run(
        ["git", "branch", "-D", branch_name],
        check=True,
    )

subprocess.run(
    ["git", "branch", branch_name, final_new],
    check=True,
)

print(f"Branch dibuat: {branch_name}")
print()
print("SELESAI.")
print()
print("JANGAN PUSH DAN JANGAN RESET MAIN DULU.")
print("Lakukan validasi berikut:")
print()
print("git log history-asli --reverse --format=\"%h | %an | %ae | %s\"")
print("git rev-list --count history-asli")
print("git show -s --format=\"%H | %an | %ae | %s\" history-asli")