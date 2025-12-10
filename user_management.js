document.addEventListener("DOMContentLoaded", function () {
    loadUsers();

    // Refresh Button
    document.getElementById("refreshBtn").addEventListener("click", loadUsers);
});

/* ================================
   LOAD USERS (READ)
================================ */
function loadUsers() {
    fetch("user_management.php?action=read")
        .then(res => res.json())
        .then(data => {
            let table = "";
            let total = data.length;
            let activeCount = data.filter(u => u.status === "active").length;

            document.getElementById("totalCount").innerText = total;
            document.getElementById("activeCount").innerText = activeCount;
            document.getElementById("showingTotal").innerText = total;

            data.forEach(user => {
                table += `
                <tr>
                    <td data-label="Select"><input type="checkbox" class="row-checkbox"></td>

                    <td data-label="User" class="primary-cell">
                        <div class="user-cell">
                            <div class="avatar-xs">${user.fullname.charAt(0).toUpperCase()}</div>
                            <div>
                                <div class="user-name">${user.fullname}</div>
                                <div class="user-id">${user.userid}</div>
                            </div>
                        </div>
                    </td>

                    <td data-label="Email">${user.email ?? "N/A"}</td>
                    <td data-label="Phone">${user.phone ?? "N/A"}</td>

                    <td data-label="Role"><span class="role-tag">${user.role}</span></td>
                    <td data-label="Kebele">${user.kebele ?? "N/A"}</td>

                    <td data-label="Status">
                        <span class="status-tag ${user.status}">${user.status}</span>
                    </td>

                    <td data-label="Created">${user.created_at}</td>

                    <td data-label="Actions">
                        <div class="action-buttons">
                            <button class="btn-icon" title="Edit" onclick="editUser(${user.id})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-icon" title="Delete" onclick="deleteUser(${user.id})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });

            document.getElementById("usersTableBody").innerHTML = table;
        });
}

/* ================================
   DELETE USER
================================ */
function deleteUser(id) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    fetch("user_management.php?action=delete&id=" + id)
        .then(res => res.text())
        .then(() => loadUsers());
}

/* ================================
   EDIT USER (OPEN POPUP)
================================ */
function editUser(id) {
    let newName = prompt("Enter new full name:");
    if (!newName) return;

    let formData = new FormData();
    formData.append("action", "update");
    formData.append("id", id);
    formData.append("fullname", newName);

    fetch("user_management.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.text())
        .then(() => loadUsers());
}
