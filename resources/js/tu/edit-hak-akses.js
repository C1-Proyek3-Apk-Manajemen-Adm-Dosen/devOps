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

document.getElementById('addAccessBtn')?.addEventListener('click', function() {
    const userSelect = document.getElementById('userSelect');
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    const userId = userSelect.value;
    const userEmail = selectedOption.dataset.email;
    const userRole = selectedOption.dataset.role;
    const permission = document.querySelector('input[name="permission"]:checked').value;
    
    if (!userId) {
        showAlert('Pilih pengguna terlebih dahulu!', 'error');
        return;
    }
    
    const existing = document.querySelector(`[data-user-id="${userId}"]`);
    if (existing) {
        showAlert('Pengguna sudah memiliki akses!', 'warning');
        return;
    }
    
    fetch(updateAccessRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            user_id: userId,
            permission: permission
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            addUserToList(userId, userEmail, userRole, permission);
            userSelect.value = '';
            document.querySelector('input[name="permission"][value="READ"]').checked = true;
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Gagal menambahkan akses', 'error');
        console.error(error);
    });
});

function addUserToList(userId, email, role, permission) {
    const accessList = document.getElementById('accessList');
    const emptyState = document.getElementById('emptyState');
    
    if (emptyState) emptyState.remove();
    
    const initials = email.substring(0, 2).toUpperCase();
    const colors = ['purple', 'green', 'blue', 'pink', 'indigo', 'red', 'yellow'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    
    const permColors = {
        'READ': 'bg-blue-100 text-blue-700 border-blue-200',
        'COMMENT': 'bg-green-100 text-green-700 border-green-200',
        'EDIT': 'bg-orange-100 text-orange-700 border-orange-200',
        'OWNER': 'bg-purple-100 text-purple-700 border-purple-200'
    };
    
    const html = `
        <div data-user-id="${userId}" class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 transition-all duration-200 group animate-slideIn">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-${randomColor}-500 to-${randomColor}-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                    ${initials}
                </div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">${email}</p>
                    <p class="text-xs text-gray-500">${role.charAt(0).toUpperCase() + role.slice(1)}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 ${permColors[permission]} text-xs font-semibold rounded-full border">
                    ${permission}
                </span>
                <button type="button" onclick="removeAccess(${userId}, '${email}')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200 opacity-0 group-hover:opacity-100" title="Hapus Akses">
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
        <span class="font-medium">${message}</span>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}
