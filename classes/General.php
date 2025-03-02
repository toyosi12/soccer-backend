<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "Crud.php";
    class General extends Crud{
        public function __construct(){
            Parent::__construct();
        }

        public function getUsers(){
            $query = "SELECT user_name, email, team_id FROM users  
                        LEFT JOIN  team_membership USING(user_id) 
                        WHERE user_type_id = 1 AND team_id IS NULL";//select only athletes without teams
            $stmt = $this->read2($query);
            if($stmt->num_rows > 0){
                while($row = $stmt->fetch_assoc()){
                    $data[] = $row;
                }

            }else{
                $data = [];
            }
            return $data;
        }

        public function getAllUsers(){
            $data = array();
            $user_id = $_SESSION['user'];
            $query = "SELECT email, first_name, last_name, phone, status, user_id, user_name,
            user_type_id, passport, school, sport, user_type FROM users JOIN user_type USING(user_type_id)
            WHERE user_id <> '$user_id'";
            $stmt = $this->read2($query);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }

        public function deleteUser($user_id){
            $query = "DELETE FROM users WHERE user_id = ?";
            $binder = array("s", "$user_id");
            return $this->delete($query, $binder);
        }

        public function createTeam($details){
            $teamName = $details['teamName'];
            $user_id = $_SESSION['user'];

            //if coach already has a team, update instead(change team name);

            $q = "SELECT * FROM teams WHERE user_id = $user_id";
            if($this->read2($q)->num_rows > 0){
                $query = "UPDATE teams SET team_name = ? WHERE user_id = ?";
                $binder = array("ss", "$teamName", "$user_id");
                return $this->update($query, $binder);
            }else{
                $query = "INSERT INTO teams (team_name, user_id) VALUES (?,?)";
                $binder = array("ss", "$teamName", "$user_id");
                if($this->create($query, $binder)){
                    //after creation of team, add coach to the team
                    $last_team_id = $this->conn->insert_id;
                    $qq = "INSERT INTO team_membership (user_id, team_id, status) VALUES (?,?,?)";
                    $bind = array("sss", "$user_id", "$last_team_id", "accepted");
                    return $this->create($qq, $bind);
                }
            }
            
        }

        public function sendInvite($details){
            $recipient = $details['inviteEmail'];
            $subject = "TEAM MEMBERSHIP REQUEST";
            $q = "SELECT user_id FROM users WHERE email = '$recipient'";
            $invitee = $this->read2($q);
            if($_invitee = $invitee->fetch_assoc()){
                $_athlete_id = $_invitee['user_id'];
                $athlete_id = $_invitee['user_id'] + 256;
            }
            $coach_id = $_SESSION['user'] + 256;

            //get team id
            $t = "SELECT team_id FROM teams WHERE user_id = ".$_SESSION['user'];
            if($team = $this->read2($t)->fetch_assoc()){
                $team_id = $team['team_id'];
            }
            $link = "https://ionicbasis.com/soccer-api/invite.php?co=$coach_id&at=$athlete_id";
            $message = "This is a team membership request on ionicbasis.com, Please follow the link below to make a decision. Thank you.
                $link
            ";
            include "mail.php";
            if($sendmail){//if mail is sent
                $qq = "INSERT INTO team_membership (user_id, team_id, status) VALUES (?,?,?)";
                $bind = array("sss", "$_athlete_id", "$team_id", "invited");
                return $this->create($qq, $bind);
            }else{
                return $resp['success'] = false;
            }
        }

        public function getMyTeam(){
            $data = array();
            $user_id = $_SESSION['user'];
            $query = "SELECT t.team_id, t.team_name, u.user_id  
                        FROM teams t JOIN team_membership te ON t.team_id = te.team_id 
                        JOIN users u ON u.user_id = te.user_id WHERE u.user_id = ?";
            $binder = array("s", "$user_id");
            $st = $this->read($query, $binder);
            if($st->num_rows > 0){
                while($row = $st->fetch_assoc()){
                    $data[] = $row;
                }

            }else{
                $data = [];
            }

            return $data;
        }

        public function getTeamMembers(){
            $data = array();
            $team_id = $_SESSION['team_id'];
            $user_id = $_SESSION['user'];
            $query = "SELECT email, first_name, last_name, phone, team_membership.status, users.user_id, user_name,
            user_type_id, passport, school, sport FROM users JOIN team_membership USING (user_id) JOIN teams USING(team_id) WHERE team_membership.team_id = ?
            AND users.user_id <> '$user_id'";
            $binder = array("s", "$team_id");
            $stm = $this->read($query, $binder);
            while($row = $stm->fetch_assoc()){
                $data[] = $row;
            }

            return $data;
        }

        public function getUserMessages(){
            $user_id = $_SESSION['user'];
            $data = array();
            $query = "select distinct u.user_name, u.passport,u. user_id 
                        from chats join users u on (u.user_id = recipient_id) 
                        join users uu on (uu.user_id = sender_id) 
                        where recipient_id = '$user_id' or sender_id = '$user_id'";
            $stmt = $this->read2($query);
            while($row  = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
            
        }

        public function saveEvents($events){
            $resp;
            $user_id = $_SESSION['user'];

            //delete all records relating to user before inserting again
            $q = "DELETE FROM calendar WHERE user_id = ?";
            $bind = array("s", "$user_id");
            if($this->delete($q, $bind)){
                for($x = 0; $x < count($events); $x++){
                    $start = array_key_exists('start', $events[$x]) ? $events[$x]['start'] : '';
                    $end = array_key_exists('end', $events[$x]) ? $events[$x]['end'] : '';
                    $title = array_key_exists('title', $events[$x]) ? $events[$x]['title']: '';
                    $color = array_key_exists('color', $events[$x]) ? json_encode($events[$x]['color']): '';
                    $actions = array_key_exists('actions', $events[$x]) ? json_encode($events[$x]['actions']) : '';
                    $allDay = array_key_exists('allDay', $events[$x]) ? $events[$x]['allDay'] : "";
                    $resizable = array_key_exists('resizable', $events[$x]) ? json_encode($events[$x]['resizable']) : "";
                    $draggable = array_key_exists('draggable', $events[$x]) ? $events[$x]['draggable'] : "";
                    $query = "INSERT INTO calendar (start, end, title, color, actions, allDay, resizable, draggable, user_id)
                                VALUES (?,?,?,?,?,?,?,?,?)";
                    $binder = array("sssssssss", "$start", "$end", "$title", "$color", "$actions", "$allDay",
                                    "$resizable", "$draggable", "$user_id");
                    $success =  $this->create($query, $binder)['success'];
                }
                if($success){
                    $resp['success'] = true;
                }else{
                    $resp['success'] = false;
                }
                return $resp;
            }else{
                $resp['success'] = false;
            }
        }

        public function getEvents(){
            $user_id = $_SESSION['user'];
            $data = array();
            $query = "SELECT * FROM calendar WHERE user_id = ?";
            $binder = array("s", "$user_id");
            $stmt = $this->read($query, $binder);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }

        public function deleteEvent($event){
            $event_id = $event['calendar_id'];
            $query = "DELETE FROM calendar WHERE calendar_id = ?";
            $binder = array("s", "$event_id");
            return $this->delete($query, $binder);
            
        }

        public function sendMessage($data){
            $message = $data['message'];
            $recipient_id = $data['recipient_id'];
            $sender_id = $_SESSION['user'];
            $query = "INSERT INTO chats(message, sender_id, recipient_id) VALUES
                        (?,?,?)";
            $binder = array("sss", "$message", "$sender_id", "$recipient_id");
            return $this->create($query, $binder);
        }

        public function getChats($details){
            $data = array();
            $sender_id = $_SESSION['user'];
            $recipient_id = $details['recipient_id'];
            $query = "SELECT * FROM chats WHERE (sender_id = ? && recipient_id = ?)
                        OR (sender_id = ? && recipient_id = ?) ORDER BY date_sent ASC";
            $binder = array("ssss", "$sender_id", "$recipient_id", "$recipient_id", "$sender_id");
            $st = $this->read($query, $binder);
            while($row = $st->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }

        public function newNotification($message){
            $message = $message['message'];
            $sender_id = $_SESSION['user'];
            $teamMembers =  $this->getTeamMembers();
            foreach($teamMembers as $teamMember){
                $recipient_id =  $teamMember['user_id'];
                $query = "INSERT INTO notifications (message, sender_id, recipient_id) VALUES (?,?,?)";
                $binder = array("sss", "$message", "$sender_id", "$recipient_id");
                $stmt = $this->create($query, $binder);
                $success =  $stmt['success'];

            }

            if($success){
                $resp['success'] = true;
            }else{
                $resp['success'] = false;
            }
            return $resp;
            
        }

        public function getNotifications(){
            $data = array();
            $user_id = $_SESSION['user'];
            $query = "SELECT * FROM notifications WHERE recipient_id = ?";
            $binder = array("s", "$user_id");
            $stmt = $this->read($query, $binder);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }

            return $data;
        }

        public function friendSuggestions(){
            $data = array();
            $data2 = array();
            $user_id = $_SESSION['user'];
            // $query = "(SELECT u1.user_name, u1.user_id, friends.accepted, friends.sender_id, friends.recipient_id 
            //             FROM friends RIGHT JOIN users u1 ON u1.user_id = friends.recipient_id 
            //             WHERE  u1.user_id <> '$user_id'
            //             AND friends.accepted <> 'Y')
            //             UNION (SELECT u2.user_name, u2.user_id, friends.accepted,  friends.sender_id, friends.recipient_id  FROM friends 
            //             RIGHT JOIN users u2 ON u2.user_id = friends.sender_id 
            //             WHERE  u2.user_id <> '$user_id'
            //             AND friends.accepted <> 'Y')";
            $query = "select distinct user_name, user_id from users left join friends on (users.user_id = friends.recipient_id) 
                        where (recipient_id is null or sender_id is null or sender_id) 
                        AND users.user_id <> '$user_id' AND friends.accepted is null";
            $stmt = $this->read2($query);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            $limit = ($stmt->num_rows < 5) ? $stmt->num_rows : 5;
            for($x = 0; $x < $limit; $x++){
                array_push($data2, $data[$x]);
            }
            return $data2;
        }

        public function addFriend($user_id){
            $sender_id = $_SESSION['user'];
            $recipient_id = $user_id['user_id'];

            //check if exists

            $q = "SELECT * FROM friends WHERE sender_id = '$sender_id' AND recipient_id='$recipient_id'";
            if($this->read2($q)->num_rows > 0){
                echo 'already exists';
                return;
            }
            $query = "INSERT INTO friends (sender_id, recipient_id) VALUES (?,?)";
            $binder = array("ss", "$sender_id", "$recipient_id");
            $stmt = $this->create($query, $binder);
            return $stmt;
        }

        public function acceptFriendRequest($user_id){
            $recipient_id = $_SESSION['user'];
            $sender_id = $user_id['user_id'];
            $query = "UPDATE friends SET accepted = 'Y' WHERE sender_id = ?
                        AND recipient_id = ?";
            $binder = array("ss", "$sender_id", "$recipient_id");
            return $this->update($query, $binder);
        }

        public function getFriends(){
            $data = array();
            $user_id = $_SESSION['user'];
            $query = "(SELECT u1.user_name, u1.user_id, friends.accepted, friends.sender_id, friends.recipient_id 
                        FROM friends JOIN users u1 ON u1.user_id = friends.recipient_id 
                        WHERE (sender_id = '$user_id' OR recipient_id = '$user_id') AND u1.user_id <> '$user_id') 
                        UNION (SELECT u2.user_name, u2.user_id, friends.accepted,  friends.sender_id, friends.recipient_id  FROM friends 
                        JOIN users u2 ON u2.user_id = friends.sender_id 
                        WHERE (sender_id = '$user_id' OR recipient_id = '$user_id') AND u2.user_id <> '$user_id')";
            $stmt = $this->read2($query);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }

        public function getCurrentUserDetails($user_id){
            $user_id = $user_id['user_id'];
            $data = array();
            $query = "SELECT email, first_name, last_name, phone, status, user_id, user_name,
            user_type_id, passport, school, sport, user_type FROM users JOIN user_type USING(user_type_id) WHERE user_id = ?";
            $binder = array("s", "$user_id");
            $stmt = $this->read($query, $binder);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }

        public function otherUser($user){
            $user_name = $user['user_name'];
            $data = array();
            $query = "SELECT email, first_name, last_name, phone, status, user_id, user_name,
            user_type_id, passport, school, sport, user_type FROM users JOIN user_type USING(user_type_id) WHERE user_name = ?";
            $binder = array("s", "$user_name");
            $stmt = $this->read($query, $binder);
            while($row = $stmt->fetch_assoc()){
                $data[] = $row;
            }
            return $data;
        }

        public function checkFriendShip($user){
            //get user_id from logged in user;
            $_data = array();
            $other_username = $user['user_name'];
            $current_user_id = $_SESSION['user'];
            $q = "SELECT user_id FROM users WHERE user_name = '$other_username'";
            $st = $this->read2($q);
            if($st->num_rows > 0){
                while($ro = $st->fetch_assoc()){
                    $otherUserId = $ro['user_id'];
                }
            }
            $query = "SELECT * FROM friends WHERE 
                (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)";

            $binder = array("ssss", "$otherUserId", "$current_user_id", "$current_user_id", "$otherUserId");
            $stmt = $this->read($query, $binder);
            while($data = $stmt->fetch_assoc()){
                $_data[] = $data;
            }

            return $_data;
        }
    }

?>