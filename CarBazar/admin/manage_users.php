<?php
$pageTitle = "Manage Users";
include 'header.php';

// Fetch all non-admin users
$users_result = $conn->query("SELECT id, full_name, username, email, is_blocked FROM users WHERE is_admin = 0 ORDER BY created_at DESC");
?>

<table>
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users_result->num_rows > 0): ?>
            <?php while($user = $users_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_blocked'] ? '<span style="color:red;">Blocked</span>' : '<span style="color:green;">Active</span>'; ?></td>
                    <td>
                        <?php if ($user['is_blocked']): ?>
                            <a href="../api/toggle_user_block.php?id=<?php echo $user['id']; ?>&action=unblock" class="btn btn-success">Unblock</a>
                        <?php else: ?>
                            <a href="../api/toggle_user_block.php?id=<?php echo $user['id']; ?>&action=block" class="btn btn-danger">Block</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php $conn->close(); ?>
            </section>
        </main>
    </div>
</body>
</html>