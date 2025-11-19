const dokumenId = window.dokumenId;
const csrfToken = window.csrfToken;
const monitoringRoute = window.monitoringRoute;
const removeAccessRoute = window.removeAccessRoute;
const updateAccessRoute = window.updateAccessRoute;

document.addEventListener('DOMContentLoaded', function() {
    openHakAksesModal();
});

window.openHakAksesModal = function() {
    const modal = document.getElementById('modalHakAkses');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

window.closeHakAksesModal = function() {
    const modal = document.getElementById('modalHakAkses');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    window.location.href = monitoringRoute;
}

document.getElementById('modalHakAkses')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeHakAksesModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHakAksesModal();
    }
});

document.getElementById('addAccessBtn')?.addEventListener('click', async function() {
    const allCheckboxes = Array.from(document.querySelectorAll('.hak-akses-checkbox'));
    const checked = allCheckboxes.filter(cb => cb.checked && !cb.closest('.user-checkbox-item').classList.contains('hidden'));

    if (checked.length === 0) {
        showAlert('Pilih pengguna terlebih dahulu!', 'error');
        return;
    }

    const permission = document.querySelector('input[name="permission"]:checked')?.value ?? 'READ';

    const btn = this;
    btn.disabled = true;
    const origTextEl = btn.querySelector('span#addBtnText');
    const origText = origTextEl ? origTextEl.textContent : btn.textContent;
    if (origTextEl) origTextEl.textContent = 'Memproses...';

    for (const cb of checked) {
        const userId = cb.value;
        const item = cb.closest('.user-checkbox-item');
        let userEmail = '';
        let userRole = 'User';
        if (item) {
            const nameEl = item.querySelector('.text-sm');
            const metaEl = item.querySelector('.text-xs');
            if (metaEl) {
                const metaText = metaEl.textContent.trim();
                const emailMatch = metaText.match(/(^\S+@\S+\.\S+)/);
                if (emailMatch) userEmail = emailMatch[1];
                const roleMatch = metaText.match(/\(([^)]+)\)/);
                if (roleMatch) userRole = roleMatch[1];
            }
            if (!userEmail && nameEl) {
                userEmail = nameEl.textContent.trim();
            }
        }

        if (document.querySelector(`[data-user-id="${userId}"]`)) {
            showAlert(`${userEmail || 'User'} sudah memiliki akses`, 'warning');
            cb.checked = false;
            continue;
        }

        try {
            const res = await fetch(updateAccessRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    user_id: userId,
                    permission: permission
                })
            });

            const data = await res.json();

            if (data.success) {
                addUserToList(userId, userEmail || (`user${userId}@local`), userRole || 'User', permission);
                cb.checked = false;
                showAlert(data.message ?? `Akses ditambahkan untuk ${userEmail}`, 'success');
            } else {
                showAlert(data.message ?? `Gagal menambahkan akses untuk ${userEmail}`, 'error');
            }
        } catch (err) {
            console.error('Error adding access for user', userId, err);
            showAlert(`Gagal menambahkan akses untuk ${userEmail}`, 'error');
        }
    }

    btn.disabled = false;
    if (origTextEl) origTextEl.textContent = origText;
});

function addUserToList(userId, email, role, permission) {
    const accessList = document.getElementById('accessList');
    const emptyState = document.getElementById('emptyState');

    if (!accessList) return;
    if (emptyState) emptyState.remove();

    const safeEmail = String(email || '');
    const initials = safeEmail.substring(0, 2).toUpperCase() || 'U';
    const colors = ['purple', 'green', 'blue', 'pink', 'indigo', 'red', 'yellow'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];

    const permColors = {
        'READ': 'bg-blue-100 text-blue-700 border-blue-200',
        'COMMENT': 'bg-green-100 text-green-700 border-green-200',
        'EDIT': 'bg-orange-100 text-orange-700 border-orange-200',
        'OWNER': 'bg-purple-100 text-purple-700 border-purple-200'
    };

    const escapedEmailForOnclick = safeEmail.replace(/'/g, "\\'");

    const html = `
        <div data-user-id="${userId}" class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 transition-all duration-200 group animate-slideIn">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-${randomColor}-500 to-${randomColor}-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                    ${initials}
                </div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">${escapeHtml(safeEmail)}</p>
                    <p class="text-xs text-gray-500">${escapeHtml((role || 'User').charAt(0).toUpperCase() + (role || 'User').slice(1))}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 ${permColors[permission] ?? 'bg-gray-100 text-gray-700 border-gray-200'} text-xs font-semibold rounded-full border">
                    ${permission}
                </span>
                <button type="button" onclick="removeAccess(${userId}, '${escapedEmailForOnclick}')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200 opacity-0 group-hover:opacity-100" title="Hapus Akses">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;

    accessList.insertAdjacentHTML('afterbegin', html);
}

window.removeAccess = function(userId, email) {
    if (!confirm(`Hapus akses untuk ${email}?`)) return;

    fetch(removeAccessRoute, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const element = document.querySelector(`[data-user-id="${userId}"]`);
            if (element) {
                element.style.opacity = '0';
                element.style.transform = 'translateX(20px)';
                setTimeout(() => element.remove(), 300);
            }
            showAlert(data.message, 'info');
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Gagal menghapus akses', 'error');
        console.error(error);
    });
}

window.saveChanges = function() {
    showAlert('Perubahan berhasil disimpan!', 'success');
    setTimeout(() => {
        window.location.href = monitoringRoute;
    }, 1000);
}

function showAlert(message, type) {
    const colors = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    };

    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-slideInRight z-[60]`;
    alert.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-medium">${escapeHtml(String(message))}</span>
    `;

    document.body.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('hakAksesDropdownTrigger');
    const menu = document.getElementById('hakAksesMenu');
    const searchInput = document.getElementById('searchUser');
    const selectAllCheckbox = document.getElementById('selectAllUsers');
    const userCheckboxes = document.querySelectorAll('.hak-akses-checkbox');
    const label = document.getElementById('hakAksesLabel');
    const addBtnText = document.getElementById('addBtnText');

    if (trigger && menu) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                searchInput?.focus();
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (trigger && menu && !trigger.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    function updateUI() {
        const selected = document.querySelectorAll('.hak-akses-checkbox:checked');
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

        const visibleCheckboxes = Array.from(userCheckboxes).filter(cb => !cb.closest('.user-checkbox-item').classList.contains('hidden'));
        const allVisibleChecked = visibleCheckboxes.every(cb => cb.checked) && visibleCheckboxes.length > 0;
        if (selectAllCheckbox) selectAllCheckbox.checked = allVisibleChecked;
    }

    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            userCheckboxes.forEach(cb => {
                if (!cb.closest('.user-checkbox-item').classList.contains('hidden')) {
                    cb.checked = isChecked;
                }
            });
            updateUI();
        });
    }

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

            updateUI();
        });
    }
});

function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}
