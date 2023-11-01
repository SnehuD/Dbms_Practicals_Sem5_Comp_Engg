<?php
// Replace 'your_database_host', 'your_database_user', 'your_database_password', and 'your_database_name' with your database credentials.
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'crud_app';


// Connect to the database
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data to prevent SQL injection
function sanitize($input)
{
    global $conn;
    return mysqli_real_escape_string($conn, $input);
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        // Add new contact
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);

        $sql = "INSERT INTO contacts (first_name, last_name, email) VALUES ('$first_name', '$last_name', '$email')";
        if ($conn->query($sql) === true) {
            ?>
            <div class="container mt-4 alert alert-success alert-dismissible fade show" role="alert">
                <p>Contact added successfully.</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // Edit contact
        $contact_id = $_POST['edit_id'];
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);

        $sql = "UPDATE contacts SET first_name='$first_name', last_name='$last_name', email='$email' WHERE id='$contact_id'";
        if ($conn->query($sql) === true) {
            ?>
            <div class="container mt-4 alert alert-success alert-dismissible fade show" role="alert">
                <p>Contact Updated successfully.</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        } else {
            echo "Error updating contact: " . $conn->error;
        }
    }
}

// Delete contact
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $contact_id = $_GET['id'];

    $sql = "DELETE FROM contacts WHERE id='$contact_id'";
    if ($conn->query($sql) === true) {
        ?>
            <div class="container mt-4 alert alert-success alert-dismissible fade show" role="alert">
                <p>Contact deleted successfully.</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
    } else {
        echo "Error deleting contact: " . $conn->error;
    }
}

// Retrieve all contacts from the database
$sql = "SELECT * FROM contacts";
$result = $conn->query($sql);
$contacts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple PHP CRUD Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h3>Add New Contact</h3>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Contact</button>
        </form>

        <hr>

        <h3>Contacts List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo $contact['id']; ?></td>
                        <td><?php echo $contact['first_name']; ?></td>
                        <td><?php echo $contact['last_name']; ?></td>
                        <td><?php echo $contact['email']; ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $contact['id']; ?>">Edit</a>
                        </td>
                        <td>
                            <a href="?action=delete&id=<?php echo $contact['id']; ?>" onclick="return confirm('Are you sure you want to delete this contact?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            // Display the edit form with contact details pre-filled
            $edit_id = $_GET['id'];
            $edit_contact = array_filter($contacts, function ($contact) use ($edit_id) {
                return $contact['id'] == $edit_id;
            });
            $edit_contact = reset($edit_contact);
        ?>
            <hr>

            <h3>Edit Contact</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $edit_contact['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $edit_contact['last_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $edit_contact['email']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Contact</button>
            </form>
        <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
