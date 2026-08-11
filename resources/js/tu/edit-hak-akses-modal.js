let allUsersData = [];
let existingAccessData = [];
let currentDokumenId = null;

document.addEventListener('DOMContentLoaded', function () {
    console.log('Edit Hak Akses Modal JS loaded (TU)');

    setupDropdownLogic();

    document.getElementById('accessList')?.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-access-btn');
        if (!btn) return;
        const userId = Number(btn.dataset.userId);
        const userName = btn.dataset.userName || 'User';
        removeAccess(userId, userName);
    });
});

/* ==============================
   OPEN / CLOSE MODAL
   ============================== */
function openEditHakAksesModal(dokumenId, dokumenJudul, dokumenNomor) {
    currentDokumenId = dokumenId;

    document.getElementById('modalDokumenJudul').textContent = escapeHtml(dokumenJudul || '-');
    document.getElementById('modalDokumenNomor').textContent = escapeHtml(dokumenNomor || '-');

    resetAddAccessForm();
    loadModalData(dokumenId);

    const modal = document.getElementById('modalEditHakAkses');
    const modalContent = document.getElementById('modalContent');
    modal?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => modalContent?.classList.add('opacity-100', 'scale-100'), 10);
}

function closeEditHakAksesModal() {
    const modal = document.getElementById('modalEditHakAkses');
    const modalContent = document.getElementById('modalContent');
    modalContent?.classList.remove('opacity-100', 'scale-100');
    modalContent?.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        modal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }, 200);
}

/* ==============================
   UTILS
   ============================== */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

/* ==============================
   LOAD DATA DARI SERVER
   ============================== */
function loadModalData(dokumenId) {
    const accessList = document.getElementById('accessList');
    accessList.innerHTML = '<div class="py-8 text-center"><p class="text-gray-500">Loading...</p></div>';

    fetch(`/tu/dokumen/${dokumenId}/modal-data`, {
        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Gagal load data');

            allUsersData = data.allUsers || [];
            existingAccessData = data.existingAccess || [];

            renderAvailableUsers();
            renderExistingAccess();
        })
        .catch(err => {
            console.error('Load modal data error:', err);
            accessList.innerHTML = `<div class="py-4 text-center text-red-500 text-sm">Gagal memuat data</div>`;
        });
}

/* ==============================
   RENDER USER YANG BELUM PUNYA AKSES 
   ============================== */
function renderAvailableUsers() {
    const container = document.getElementById('userListContainer');
    if (!container) return;
    container.innerHTML = '';

    const usersTanpaAkses = allUsersData.filter(user =>
        !existingAccessData.some(acc => acc.grantee_user_id === user.id_user)
    );

    if (usersTanpaAkses.length === 0) {
        container.innerHTML = `<div class="py-6 text-center text-sm text-gray-500">Semua pengguna sudah memiliki akses</div>`;
        return;
    }

    usersTanpaAkses.forEach(user => {
        const checkboxId = `user-checkbox-${user.id_user}`;
        const html = `
        <label for="${checkboxId}" class="user-checkbox-item flex items-center px-4 py-3 hover:bg-gray-50 rounded-lg cursor-pointer transition group border-b border-gray-100 last:border-0"
               data-search="${escapeHtml((user.nama_lengkap || '') + ' ' + (user.email || '')).toLowerCase()}"
               data-user-id="${user.id_user}">
            <input type="checkbox" id="${checkboxId}"
                   name="user_ids[]"
                   value="${user.id_user}"
                   class="hak-akses-checkbox w-5 h-5 text-[#050C9C] rounded border-gray-300 focus:ring-[#050C9C] focus:ring-2">
            <div class="ml-3 flex-1 select-none">
                <div class="text-sm font-medium text-gray-900 group-hover:text-[#050C9C] transition-colors">
                    ${escapeHtml(user.nama_lengkap)}
                </div>
                <div class="text-xs text-gray-500">
                    ${escapeHtml(user.email)} <span class="text-gray-400">(${escapeHtml(user.role)})</span>
                </div>
            </div>
        </label>`;
        container.insertAdjacentHTML('beforeend', html);
    });

    setupUserCheckboxes();
}

/* ==============================
   RENDER USER YANG SUDAH PUNYA AKSES (bagian bawah)
   ============================== */
function renderExistingAccess() {
    const accessList = document.getElementById('accessList');
    if (!accessList) return;
    accessList.innerHTML = '';

    if (existingAccessData.length === 0) {
        accessList.innerHTML = `
            <div class="py-10 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500 font-medium text-sm">Belum ada yang memiliki akses</p>
            </div>`;
        return;
    }

    existingAccessData.forEach(access => {
        const user = access.grantee_user;
        if (!user) return;

        const initials = (user.nama_lengkap || 'U').substring(0, 2).toUpperCase();
        const permColors = {
            READ: 'bg-blue-100 text-blue-700 border-blue-200',
            COMMENT: 'bg-green-100 text-green-700 border-green-200',
            EDIT: 'bg-orange-100 text-orange-700 border-orange-200',
            OWNER: 'bg-purple-100 text-purple-700 border-purple-200'
        };

        const html = `
            <div class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 transition-all group" data-user-id="${user.id_user}">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        ${escapeHtml(initials)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-800 truncate">${escapeHtml(user.nama_lengkap)}</p>
                        <p class="text-xs text-gray-500 truncate">${escapeHtml(user.email)} (${escapeHtml(user.role)})</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 ${permColors[access.perm] || 'bg-gray-100 text-gray-700'} text-xs font-semibold rounded-full border">
                        ${escapeHtml(access.perm)}
                    </span>
                    <!-- tombol hapus sekarang tanpa inline onclick; pake data-* -->
                    <button type="button"
                            class="remove-access-btn p-2 text-red-500 hover:bg-red-50 rounded-lg transition opacity-0 group-hover:opacity-100"
                            title="Hapus Akses"
                            data-user-id="${user.id_user}"
                            data-user-name="${escapeHtml(user.nama_lengkap)}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>`;
        accessList.insertAdjacentHTML('beforeend', html);
    });
}

function setupDropdownLogic() {
    const trigger = document.getElementById('hakAksesDropdownTrigger');
    const menu = document.getElementById('hakAksesMenu');
    const searchInput = document.getElementById('searchUser');
    const selectAll = document.getElementById('selectAllUsers');

    trigger?.addEventListener('click', e => {
        e.stopPropagation();
        menu?.classList.toggle('hidden');
        if (!menu?.classList.contains('hidden')) searchInput?.focus();
    });

    document.addEventListener('click', e => {
        if (trigger && menu && !trigger.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    searchInput?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        let visible = false;
        document.querySelectorAll('.user-checkbox-item').forEach(item => {
            const text = item.getAttribute('data-search') || '';
            if (text.includes(q)) {
                item.classList.remove('hidden');
                visible = true;
            } else {
                item.classList.add('hidden');
            }
        });
        document.getElementById('noUserFound')?.classList.toggle('hidden', visible);
        updateUIState();
    });

    if (menu) {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    if (selectAll) {
        selectAll.style.pointerEvents = 'auto';

        selectAll.addEventListener('click', function (e) {
            e.stopPropagation();

            const isChecked = this.checked;
            document.querySelectorAll('.hak-akses-checkbox').forEach(cb => {
                const parent = cb.closest('.user-checkbox-item');
                if (!parent || !parent.classList.contains('hidden')) {
                    cb.checked = isChecked;
                }
            });

            updateUIState();
        });
    }

}

function setupUserCheckboxes() {
    document.querySelectorAll('.hak-akses-checkbox').forEach(cb => {
        cb.addEventListener('change', updateUIState);
    });
}

function updateUIState() {
    const checked = document.querySelectorAll('.hak-akses-checkbox:checked').length;
    const label = document.getElementById('hakAksesLabel');
    const btnText = document.getElementById('addBtnText');

    if (checked > 0) {
        if (label) {
            label.textContent = `${checked} Pengguna Dipilih`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-semibold');
        }
        if (btnText) btnText.textContent = `Tambah Akses (${checked})`;
    } else {
        if (label) {
            label.textContent = 'Pilih pengguna...';
            label.classList.remove('text-gray-900', 'font-semibold');
            label.classList.add('text-gray-500');
        }
        if (btnText) btnText.textContent = 'Tambah Akses';
    }
}

/* ==============================
   TAMBAH AKSES
   ============================== */
document.getElementById('addAccessBtn')?.addEventListener('click', handleAddAccess);

async function handleAddAccess() {
    const checkedBoxes = Array.from(document.querySelectorAll('.hak-akses-checkbox:checked'));
    if (checkedBoxes.length === 0) {
        Swal.fire({ title: 'Pilih pengguna dulu!', icon: 'warning', confirmButtonColor: '#f59e0b', customClass: { popup: 'rounded-2xl' } });
        return;
    }

    const permission = document.querySelector('input[name="permission"]:checked')?.value || 'READ';
    const btn = document.getElementById('addAccessBtn');
    const originalHTML = btn?.innerHTML || '';

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menambahkan...`;
    }

    const csrf = getCsrfToken();

    const promises = checkedBoxes.map(cb => {
        const userId = cb.value;
        const name = cb.closest('label')?.querySelector('.text-sm.font-medium')?.textContent?.trim() || 'User';
        return fetch(`/tu/dokumen/${currentDokumenId}/hak-akses`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: userId, permission })
        }).then(async res => {
            if (!res.ok) {
                const text = await res.text().catch(() => res.statusText);
                throw new Error(`HTTP ${res.status}: ${text}`);
            }
            const json = await res.json().catch(() => ({ success: false, message: 'Invalid JSON response' }));
            return { name, result: json };
        }).catch(err => ({ name, error: err.message }));
    });

    const results = await Promise.allSettled(promises);

    let success = 0;
    const errors = [];

    results.forEach(r => {
        if (r.status === 'fulfilled') {
            const payload = r.value;
            if (payload.error) errors.push(`${payload.name}: ${payload.error}`);
            else if (payload.result?.success) success++;
            else errors.push(`${payload.name}: ${payload.result?.message || 'Gagal'}`);
        } else {
            errors.push(`Unknown error: ${r.reason}`);
        }
    });

    if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }

    if (success) {
        Swal.fire({ title: 'Berhasil!', html: `<strong>${success}</strong> akses ditambahkan`, icon: 'success', timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
        setTimeout(() => loadModalData(currentDokumenId), 500);
    }
    if (errors.length) {
        Swal.fire({ title: 'Ada error', html: errors.join('<br>'), icon: 'error', confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-2xl' } });
    }
}

/* ==============================
   HAPUS AKSES
   ============================== */
async function removeAccess(userId, userName) {
    const { isConfirmed } = await Swal.fire({
        title: 'Hapus Akses?',
        html: `Yakin menghapus akses <strong>${escapeHtml(userName)}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl' }
    });

    if (!isConfirmed) return;

    try {
        const res = await fetch(`/tu/dokumen/${currentDokumenId}/hak-akses`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ user_id: userId })
        });

        if (!res.ok) {
            const text = await res.text().catch(() => res.statusText);
            throw new Error(`HTTP ${res.status}: ${text}`);
        }

        const data = await res.json().catch(() => ({ success: false, message: 'Invalid JSON response' }));

        if (data.success) {
            Swal.fire({ title: 'Berhasil!', text: `${userName} dihapus`, icon: 'success', timer: 1500, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
            setTimeout(() => loadModalData(currentDokumenId), 500);
        } else {
            throw new Error(data.message || 'Gagal');
        }
    } catch (e) {
        Swal.fire({ title: 'Error!', text: e.message, icon: 'error', confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-2xl' } });
    }
}

/* ==============================
   UTILITAS
   ============================== */
function resetAddAccessForm() {
    document.getElementById('addAccessForm')?.reset();
    document.querySelectorAll('.hak-akses-checkbox').forEach(c => c.checked = false);
    if (document.getElementById('selectAllUsers')) document.getElementById('selectAllUsers').checked = false;
    if (document.getElementById('searchUser')) document.getElementById('searchUser').value = '';
    document.querySelectorAll('.user-checkbox-item').forEach(i => i.classList.remove('hidden'));
    updateUIState();
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !document.getElementById('modalEditHakAkses')?.classList?.contains('hidden')) {
        closeEditHakAksesModal();
    }
});

/* Export ke global */
window.openEditHakAksesModal = openEditHakAksesModal;
window.closeEditHakAksesModal = closeEditHakAksesModal;
window.removeAccess = removeAccess;

console.log('Edit Hak Akses Modal (TU) - Semua fungsi siap!');
