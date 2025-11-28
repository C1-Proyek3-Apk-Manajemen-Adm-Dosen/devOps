let allUsersData = [];
let existingAccessData = []; 
let currentDokumenId = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Edit Hak Akses Modal JS loaded');
    setupDropdownLogic();
});

/**
 * Buka modal edit hak akses
 */
function openEditHakAksesModal(dokumenId, dokumenJudul, dokumenNomor) {
    currentDokumenId = dokumenId;
    
    const modal = document.getElementById('modalEditHakAkses');
    const modalContent = document.getElementById('modalContent');
    
    // Update header modal
    document.getElementById('modalDokumenJudul').textContent = escapeHtml(dokumenJudul || '-');
    document.getElementById('modalDokumenNomor').textContent = escapeHtml(dokumenNomor || '-');
    
    // Reset form
    resetAddAccessForm();
    
    // Load data dari API
    loadModalData(dokumenId);
    
    // Tampilkan modal
    if (modal && modalContent) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        setTimeout(() => {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }, 10);
    }
}

/**
 * Tutup modal
 */
function closeEditHakAksesModal() {
    const modal = document.getElementById('modalEditHakAkses');
    const modalContent = document.getElementById('modalContent');
    
    if (modal && modalContent) {
        modalContent.classList.remove('opacity-100', 'scale-100');
        modalContent.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    }
}

/**
 * Load data dari API: semua user + existing access
 */
function loadModalData(dokumenId) {
    const accessList = document.getElementById('accessList');
    accessList.innerHTML = '<div class="py-8 text-center"><p class="text-gray-500">Loading...</p></div>';
    
    fetch(`/dosen/dokumen/${dokumenId}/modal-data`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('📊 Modal data fetched:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'Gagal load data');
        }
        
        allUsersData = data.allUsers || [];
        existingAccessData = data.existingAccess || [];
        
        // Render daftar user untuk dipilih
        renderAvailableUsers();
        
        // Render daftar user yang sudah punya akses
        renderExistingAccess();
        
    })
    .catch(err => {
        console.error('❌ Load modal data error:', err);
        accessList.innerHTML = `
            <div class="py-4 text-center text-red-500">
                <p class="text-sm">❌ ${err.message || 'Gagal memuat data'}</p>
            </div>
        `;
    });
}

/**
 * Render daftar user yang tersedia untuk dipilih
 */
function renderAvailableUsers() {
    const container = document.getElementById('userListContainer');
    container.innerHTML = '';
    
    // Filter: hanya user yang belum punya akses
    const usersTanpaAkses = allUsersData.filter(user => {
        return !existingAccessData.some(access => access.grantee_user_id === user.id_user);
    });
    
    console.log(`👥 Users tanpa akses: ${usersTanpaAkses.length}`);
    
    if (usersTanpaAkses.length === 0) {
        container.innerHTML = `
            <div class="py-4 text-center text-sm text-gray-500">
                <p>Semua pengguna sudah memiliki akses</p>
            </div>
        `;
        return;
    }
    
    usersTanpaAkses.forEach(user => {
        const label = document.createElement('label');
        label.className = 'user-checkbox-item flex items-center px-3 py-2.5 hover:bg-gray-50 rounded-lg cursor-pointer transition group';
        label.setAttribute('data-search', `${user.nama_lengkap} ${user.email}`.toLowerCase());
        label.setAttribute('data-user-id', user.id_user);
        
        label.innerHTML = `
            <input type="checkbox" name="user_ids[]" value="${user.id_user}" class="hak-akses-checkbox w-4 h-4 text-[#050C9C] border-gray-300 rounded focus:ring-[#050C9C] focus:ring-2">
            <div class="ml-3 flex-1">
                <div class="text-sm font-medium text-gray-900 group-hover:text-[#050C9C] transition-colors">${escapeHtml(user.nama_lengkap)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(user.email)} (${escapeHtml(user.role)})</div>
            </div>
        `;
        
        container.appendChild(label);
    });
    
    setupUserCheckboxes();
}

/**
 * Render daftar user yang sudah punya akses
 */
function renderExistingAccess() {
    const accessList = document.getElementById('accessList');
    accessList.innerHTML = '';
    
    console.log(`🔐 Existing access count: ${existingAccessData.length}`);
    
    if (existingAccessData.length === 0) {
        accessList.innerHTML = `
            <div id="emptyState" class="py-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500 font-medium text-sm">Belum ada yang memiliki akses</p>
                <p class="text-gray-400 text-xs">Tambahkan pengguna untuk memberikan akses</p>
            </div>
        `;
        return;
    }
    
    existingAccessData.forEach(access => {
        const user = access.grantee_user;
        if (!user) return;
        
        const initials = (user.nama_lengkap || 'U').substring(0, 2).toUpperCase();
        
        const permColors = {
            'READ': 'bg-blue-100 text-blue-700 border-blue-200',
            'COMMENT': 'bg-green-100 text-green-700 border-green-200',
            'EDIT': 'bg-orange-100 text-orange-700 border-orange-200',
            'OWNER': 'bg-purple-100 text-purple-700 border-purple-200'
        };
        
        const html = `
            <div data-user-id="${user.id_user}" class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 transition-all duration-200 group">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md flex-shrink-0">
                        ${initials}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-800 truncate">${escapeHtml(user.nama_lengkap)}</p>
                        <p class="text-xs text-gray-500 truncate">${escapeHtml(user.email)} (${escapeHtml(user.role)})</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                    
                    <!-- Permission Badge -->
                    <span class="px-3 py-1 ${permColors[access.perm] || 'bg-gray-100 text-gray-700 border-gray-200'} text-xs font-semibold rounded-full border whitespace-nowrap">
                        ${access.perm}
                    </span>
                    
                    <!-- Tombol Hapus -->
                    <button type="button" onclick="removeAccess(${user.id_user}, '${escapeHtml(user.nama_lengkap)}')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200 opacity-0 group-hover:opacity-100 flex-shrink-0" title="Hapus Akses">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        accessList.insertAdjacentHTML('beforeend', html);
    });
}

/**
 * Setup dropdown logic
 */
function setupDropdownLogic() {
    const trigger = document.getElementById('hakAksesDropdownTrigger');
    const menu = document.getElementById('hakAksesMenu');
    const searchInput = document.getElementById('searchUser');
    const selectAllCheckbox = document.getElementById('selectAllUsers');
    
    if (trigger && menu) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                searchInput?.focus();
            }
        });
    }
    
    document.addEventListener('click', (e) => {
        if (trigger && menu && !trigger.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            let hasResult = false;
            
            document.querySelectorAll('.user-checkbox-item').forEach(item => {
                const searchText = item.getAttribute('data-search') || '';
                if (searchText.includes(query)) {
                    item.classList.remove('hidden');
                    hasResult = true;
                } else {
                    item.classList.add('hidden');
                }
            });
            
            const noResult = document.getElementById('noUserFound');
            if (noResult) {
                if (hasResult) noResult.classList.add('hidden');
                else noResult.classList.remove('hidden');
            }
            
            updateUIState();
        });
    }
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.hak-akses-checkbox').forEach(cb => {
                if (!cb.closest('.user-checkbox-item')?.classList.contains('hidden')) {
                    cb.checked = isChecked;
                }
            });
            updateUIState();
        });
    }
}

/**
 * Setup user checkboxes
 */
function setupUserCheckboxes() {
    const userCheckboxes = document.querySelectorAll('.hak-akses-checkbox');
    
    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateUIState);
    });
}

/**
 * Update UI state
 */
function updateUIState() {
    const selected = document.querySelectorAll('.hak-akses-checkbox:checked');
    const label = document.getElementById('hakAksesLabel');
    const addBtnText = document.getElementById('addBtnText');
    const count = selected.length;
    
    if (label) {
        if (count > 0) {
            label.textContent = `${count} Pengguna Dipilih`;
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900', 'font-semibold');
            if (addBtnText) addBtnText.textContent = `Tambah Akses (${count})`;
        } else {
            label.textContent = 'Pilih pengguna...';
            label.classList.remove('text-gray-900', 'font-semibold');
            label.classList.add('text-gray-500');
            if (addBtnText) addBtnText.textContent = 'Tambah Akses';
        }
    }
}

/**
 * Handle tambah akses
 */
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addAccessBtn');
    if (addBtn) {
        addBtn.addEventListener('click', handleAddAccess);
    }
});

async function handleAddAccess() {
    const checked = Array.from(document.querySelectorAll('.hak-akses-checkbox:checked'));
    
    if (checked.length === 0) {
        Swal.fire({
            title: 'Peringatan!',
            text: 'Pilih pengguna terlebih dahulu!',
            icon: 'warning',
            confirmButtonColor: '#f59e0b',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
        return;
    }
    
    const permission = document.querySelector('input[name="permission"]:checked')?.value ?? 'READ';
    const btn = document.getElementById('addAccessBtn');
    
    const originalBtnHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Menambahkan...</span>
    `;
    
    let successCount = 0;
    let errorMessages = [];
    
    for (const checkbox of checked) {
        const userId = checkbox.value;
        const item = checkbox.closest('.user-checkbox-item');
        const userName = item.querySelector('.text-sm.font-medium')?.textContent?.trim() || 'User';
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                throw new Error('CSRF token tidak ditemukan');
            }
            
            const res = await fetch(`/dosen/dokumen/${currentDokumenId}/hak-akses`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    permission: permission
                })
            });
            
            if (!res.ok) {
                const errorText = await res.text();
                console.error('❌ Response error:', errorText);
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            
            const data = await res.json();
            
            if (data.success) {
                successCount++;
                console.log(`✅ ${userName} - ${data.message}`);
            } else {
                errorMessages.push(`${userName}: ${data.message}`);
            }
        } catch (err) {
            console.error('❌ Error:', err);
            errorMessages.push(`${userName}: ${err.message}`);
        }
    }
    
    btn.disabled = false;
    btn.innerHTML = originalBtnHTML;
    
    if (successCount > 0) {
        Swal.fire({
            title: 'Berhasil!',
            html: `<strong>${successCount}</strong> akses berhasil ditambahkan`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-2xl'
            }
        });
        
        setTimeout(() => loadModalData(currentDokumenId), 500);
    }
    
    if (errorMessages.length > 0) {
        Swal.fire({
            title: 'Ada Error!',
            html: errorMessages.join('<br>'),
            icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    }
}

/**
 * Remove akses
 */
async function removeAccess(userId, userName) {
    const result = await Swal.fire({
        title: 'Hapus Akses?',
        html: `Apakah Anda yakin ingin menghapus akses untuk <strong>${escapeHtml(userName)}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold shadow-lg',
            cancelButton: 'px-6 py-2.5 rounded-xl font-semibold'
        }
    });

    if (!result.isConfirmed) return;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token tidak ditemukan');
        }
        
        const res = await fetch(`/dosen/dokumen/${currentDokumenId}/hak-akses`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        if (!res.ok) {
            const errorText = await res.text();
            console.error('❌ Response error:', errorText);
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: `${userName} berhasil dihapus`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
            
            setTimeout(() => loadModalData(currentDokumenId), 500);
        } else {
            Swal.fire({
                title: 'Gagal!',
                text: data.message || 'Gagal hapus akses',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        }
    } catch (err) {
        console.error('❌ Error:', err);
        
        Swal.fire({
            title: 'Error!',
            text: `Gagal hapus akses: ${err.message}`,
            icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    }
}

/**
 * Reset form
 */
function resetAddAccessForm() {
    const form = document.getElementById('addAccessForm');
    if (form) form.reset();
    
    document.querySelectorAll('.hak-akses-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllUsers').checked = false;
    document.getElementById('searchUser').value = '';
    document.querySelectorAll('.user-checkbox-item').forEach(item => item.classList.remove('hidden'));
    updateUIState();
}

/**
 * Escape HTML
 */
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Keyboard handler
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('modalEditHakAkses');
        if (modal && !modal.classList.contains('hidden')) {
            closeEditHakAksesModal();
        }
    }
});

window.openEditHakAksesModal = openEditHakAksesModal;
window.closeEditHakAksesModal = closeEditHakAksesModal;
window.removeAccess = removeAccess;

console.log('✅ Edit Hak Akses Modal - All functions ready');