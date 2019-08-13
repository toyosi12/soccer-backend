<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include "classes/config.php";
    $conn = mysqli_connect(db_host, db_user, db_pass, db_name);
    if(!$conn){
        die("could not establish connection to the database");
        return;
    }
    //accept or decline
    if(isset($_POST['accept'])){
        $st = mysqli_query($conn, "UPDATE team_membership SET status = 'accepted' WHERE team_id = ".$_SESSION['team_id']."
                            AND user_id = ". $_SESSION['athlete_id']);
        if($st){
            echo "<script>alert('You have accepted the offer')</script>";
        }else{
            echo "<script>alert('failed');</script>";
        }
        //header("Location: http://localhost:8080");
        
    }
    
    if(isset($_GET['co']) && isset($_GET['at'])){
        $coach_id = $_GET['co'] - 256;
        $athlete_id = $_GET['at'] - 256;
        $stmt = mysqli_query($conn, "SELECT last_name, first_name, team_name, team_id FROM 
                                    users JOIN teams using(user_id) WHERE user_id = '$coach_id'");
        if($row = $stmt->fetch_assoc()){
            $coach_name = $row['last_name']. " " . $row['first_name'];
            $teamName = $row['team_name'];
            $teamId = $row['team_id'];
            $_SESSION['team_id'] = $teamId;
            $_SESSION['athlete_id'] = $athlete_id;
        }

        $stmt2 = mysqli_query($conn, "SELECT last_name, first_name, user_name FROM users WHERE
                                        user_id = '$athlete_id'");
        if($roww = $stmt2->fetch_assoc()){
            $athlete_name = $roww['last_name']. " " . $roww['first_name'];
        }
        
        
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Ionicbasis Invite</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            </head>
            <body>
                <div class="container">
                    <div class="col-md-8 mx-auto">
                        <div class="card shadow shadow-lg mt-5">
                            <div class="card-body">
                                <p>Dear <?php echo $athlete_name; ?>, <br />
                                    You have an invite to join a team on ionicbasis.com. Please click any of 
                                    the buttons below to make your decison. Thank you.
                                </p>
                                <table class="table">
                                    <tr>
                                        <td>Team</td>
                                        <td><?php echo $teamName; ?></td>
                                    </tr>

                                    <tr>
                                            <td>Coach</td>
                                            <td><?php echo $coach_name; ?></td>
                                        </tr>
                                </table>
                                <div>
                                <form action="invite.php" method="post">
                                    <button type="submit" class="btn btn-primary" name="accept">Accept</button>
                                    <button type="button" class="btn btn-danger" name="decline" onclick="alert('declined')">Decline</button>
                                </form>
                                <p><a href="http://localhost:8080">Visit site</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
           
        </html>
<?php
    }
?>