<?php 
session_start(); 
include('db_connection.php'); 
include('adminheader.php');

// Fetch pending doctor requests 
$query = "SELECT * FROM doctorreg WHERE status = 'Pending'"; 
$result = mysqli_query($conn, $query); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pending Doctor Approvals</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #00b4d8;
            --bg-color: #f4f7f6;
            --text-color: #333;
            --white: #ffffff;
        }
        .reject-btn {
    background: #d9534f;
    margin-left: 5px;
}

.reject-btn:hover {
    background: #c9302c;
}


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: var(--bg-color);
            line-height: 1.6;
        }

        .container2 {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            background: var(--white);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .header {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem;
            text-align: center;
        }

        .pending-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .pending-table th {
            background: var(--secondary-color);
            color: var(--white);
            padding: 1rem;
            text-align: left;
        }

        .pending-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .action-btn:hover {
            background: #005b8c;
        }

        .certificate-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .certificate-link:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        

    </style>
</head>
<body>
<body>
    <!-- Sidebar -->
    

    

    
 <br>
 <br>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="pending-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Experience</th>
                        <th>Specialization</th>
                        <th>Qualification</th>
                        <th>Degree Certificate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['experience']); ?> years</td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($row['qualifications']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($row['degree_certificate']); ?>" 
                                   class="certificate-link" 
                                   target="_blank">
                                    View Certificate
                                </a>
                            </td>
                            
                            <td>
    <a href="approvedoct.php?id=<?php echo $row['id']; ?>" 
       class="action-btn" 
       onclick="return confirm('Approve this doctor?')">
        Approve
    </a>
    <a href="rejectdoctor.php?id=<?php echo $row['id']; ?>" 
       class="action-btn reject-btn" 
       onclick="return confirm('Reject this doctor?')">
        Reject
    </a>
</td>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--primary-color);"></i>
                <p>No pending doctor approvals at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
<script>
        document.addEventListener("DOMContentLoaded", function () {
            const userIcon = document.getElementById("user-icon");
            const dropdown = document.getElementById("user-dropdown");

            userIcon.addEventListener("click", function (event) {
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                event.stopPropagation();
            });

            document.addEventListener("click", function (event) {
                if (!userIcon.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.style.display = "none";
                }
            });
        });
    </script>
</html>