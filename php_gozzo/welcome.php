<?php
include 'db.php';
session_start();

$session_user = $_SESSION['user_name'] ?? null;
if (!$session_user) { 
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$dept_id = $_SESSION['id_dept'];
$is_admin = ($dept_id == 6);

if ($is_admin) {
    
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        mysqli_query($conn, "DELETE FROM employees WHERE id = $id AND email != '$user_email'");
        
        header("Location: welcome.php");
        exit();
    }

    if (isset($_POST['btn_save'])) {
        $id = intval($_POST['id_emp'] ?? 0);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $last = mysqli_real_escape_string($conn, $_POST['last_name']);
        $date = $_POST['date']; 
        $mail = mysqli_real_escape_string($conn, $_POST['email']);
        $pass = $_POST['pass'];
        $dept = intval($_POST['dept']);
        $sal  = floatval($_POST['salary']);

        if ($id > 0) {
            $sql = "UPDATE employees SET name='$name', last_name='$last', start_date='$date', 
                    email='$mail', password='$pass', id_department='$dept', base_salary='$sal' WHERE id=$id";
        } else {
            $sql = "INSERT INTO employees (name, last_name, start_date, email, password, id_department, base_salary) 
                    VALUES ('$name', '$last', '$date', '$mail', '$pass', '$dept', '$sal')";
        }
        mysqli_query($conn, $sql);
        
        header("Location: welcome.php");
        exit();
    }
}

$user_query = "SELECT e.*, d.name AS dname FROM employees e JOIN departments d ON e.id_department = d.id WHERE e.email = '$user_email'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Calculating Payroll
function getPayroll($base) {
    $bonus = $base * 0.10;
    $day = $base / 30;
    $hour = $day / 8;
    $extra = (5 * ($hour * 1.5)) + (1 * ($day * 1.5));
    return [
        'bonus' => $bonus, 
        'extra' => $extra, 
        'total' => $base + $bonus + $extra
    ];
}

$base_salary = floatval($user_data['base_salary'] ?? 0);
$pay = getPayroll($base_salary);

$edit_id = $_GET['edit'] ?? null;
$row_edit = null;
if ($edit_id && $is_admin) {
    $row_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employees WHERE id = " . intval($edit_id)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to my Nomina project</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 1100px; margin: auto; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .field { margin-bottom: 18px; }
        .label { font-size: 0.75rem; font-weight: bold; color: #777; text-transform: uppercase; letter-spacing: 1px; }
        .value { font-size: 1.1rem; font-weight: 500; margin-top: 4px; }
        .salary-summary { background: #f0f9ff; border: 1px solid #cceeff; padding: 25px; border-radius: 10px; text-align: center; }
        .salary-summary h3 { margin: 10px 0; font-size: 2.2rem; color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 14px; border-bottom: 1px solid #ddd; }
        th { background: #343a40; color: white; font-size: 0.85rem; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 20px; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-save { background: #28a745; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-logout { color: #dc3545; text-decoration: none; font-weight: bold; border: 1px solid #dc3545; padding: 8px 15px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">

    <div class="card">
        <div class="header">
            <h2 style="margin:0;">Employee Profile</h2>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>

        <div class="info-grid">
            <div>
                <div class="field">
                    <div class="label">Full Name</div>
                    <div class="value"><?php echo htmlspecialchars(($user_data['name'] ?? '') . " " . ($user_data['last_name'] ?? '')); ?></div>
                </div>
                <div class="field">
                    <div class="label">E-mail</div>
                    <div class="value"><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></div>
                </div>
                <div class="field">
                    <div class="label">Department</div>
                    <div class="value"><?php echo htmlspecialchars($user_data['dname'] ?? ''); ?></div>
                </div>
                <div class="field">
                    <div class="label">Start Date</div>
                    <div class="value">
                        <?php 
                            if (!empty($user_data['start_date'])) {
                                echo date("d-M-Y", strtotime($user_data['start_date']));
                            } else {
                                echo "Not available";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="salary-summary">
                <div class="label">Monthly Income</div>
                <h3>$<?php echo number_format($pay['total'], 2); ?></h3>
                <div style="font-size: 0.9rem; color: #555; margin-top: 15px;">
                    Base Salary: $<?php echo number_format($base_salary, 2); ?> | 
                    Bonus: $<?php echo number_format($pay['bonus'] + $pay['extra'], 2); ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_admin): ?>
    <div class="card" style="border-top: 5px solid #007bff;">
        <h3 style="color: #007bff;"><?php echo $row_edit ? "Update Employee Record" : "Register New Employee"; ?></h3>
        
        <form method="POST" action="welcome.php">
            <input type="hidden" name="id_emp" value="<?php echo $row_edit['id'] ?? 0; ?>">
            <div class="form-row">
                <input type="text" name="name" placeholder="First Name" value="<?php echo $row_edit['name'] ?? ''; ?>" required>
                <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $row_edit['last_name'] ?? ''; ?>" required>
                <input type="date" name="date" value="<?php echo $row_edit['start_date'] ?? ''; ?>" required>
                <input type="email" name="email" placeholder="E-mail" value="<?php echo $row_edit['email'] ?? ''; ?>" required>
                <input type="text" name="pass" placeholder="Password" value="<?php echo $row_edit['password'] ?? ''; ?>" required>
                <select name="dept" required>
                    <?php 
                    $depts = ["Development", "Design", "QA", "Marketing", "Accounting and Sales", "Human Resources"];
                    foreach($depts as $idx => $dept_name) {
                        $val = $idx + 1;
                        $sel = ($row_edit && $row_edit['id_department'] == $val) ? "selected" : "";
                        echo "<option value='$val' $sel>$dept_name</option>";
                    }
                    ?>
                </select>
                <input type="number" name="salary" placeholder="Base Salary" step="0.01" value="<?php echo $row_edit['base_salary'] ?? ''; ?>" required>
                
                <button type="submit" name="btn_save" class="btn-save">SAVE</button>
            </div>
        </form>

        <h3 style="margin-top: 40px;">SUPERCINES ADMINISTRATIVE STAFF LIST</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Department</th>
                    <th>Start Date</th>
                    <th>Monthly Income</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $all_staff = mysqli_query($conn, "SELECT e.*, d.name AS dname FROM employees e JOIN departments d ON e.id_department = d.id");
                while($staff = mysqli_fetch_assoc($all_staff)): 
                    $calc = getPayroll($staff['base_salary']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($staff['name'] . " " . $staff['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                    <td><?php echo htmlspecialchars($staff['dname']); ?></td>
                    <td>
                        <?php 
                            if (!empty($staff['start_date'])) {
                                echo date("d-M-Y", strtotime($staff['start_date']));
                            } else {
                                echo "Not set";
                            }
                        ?>
                    </td>
                    <td><strong>$<?php echo number_format($calc['total'], 2); ?></strong></td>
                    <td>
                        <a href="welcome.php?edit=<?php echo $staff['id']; ?>" style="color: #007bff; text-decoration: none;">Edit</a> | 
                        <a href="welcome.php?delete=<?php echo $staff['id']; ?>" style="color: #dc3545; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this Employee?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

</body>
</html>