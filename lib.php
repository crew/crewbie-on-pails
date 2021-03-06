<?php

# Get the request url
# getURL : Nothing -> PHPArray of the url
# If the url is http://crew.wvh.io/user/spuner,
# the function will return: array("user", "spuner");
function getURL() {
  $url = explode('/', $_SERVER['REQUEST_URI']);
  array_shift($url); # This is because the first one is always null
  if(strlen($url[0]) < 3)
    $url[0] = "home";

  return $url;
}

# Loads a page from the pages folder. This will go straight to ouput
# Returns nothing.
function getPage($page) {
  if(file_exists("pages/$page.php"))
    include("pages/$page.php");
  else
    include("pages/404.php");
}

# Load the crewbie.json file and convert into a php array
function loadUsers(){
  return json_decode(file_get_contents("crewbie.json"));
}

# Add a user to the datastore. Returns nothing
function addUser($name, $points, $achievements = array()){
  $existing_users = loadUsers();
  $existing_users[] = array("name"=>$name, "points"=>$points, "achievements"=>explode(',',$achievements));
  saveUsers($existing_users);

}

# Saves the array into the datastore for users
function saveUsers($users){
  file_put_contents("crewbie.json", json_encode(array_map("sanitize_map", $users)));
}

# This function assumes the user already exists in the datastore and will apply all attributes
function editUser($name, $points, $achievements = array()){
  $users = loadUsers();
  $achievements = explode(",",$achievements);
  foreach($users as $user){
    if($name == $user->name){
      $user->points = $points;
      $user->achievements = $achievements;
      break;
    }
  }
  saveUsers($users);
}

# Gets an individual user based on name
function getUser($name){
  $users = loadUsers();
  $i = 0;
  for(;$i < count($users); $i++){
    if($users[$i]->name == $name)
      break;
  }
  return $users[$i];
}

# sanitize_map, takes either a string or an array and applies sanitize
function sanitize_map($input){
  if(is_array($input))
    array_map("sanitize", $input);
  else
    sanitize($input);

  return $input;
}

# sanitize some user input
function sanitize($input){
  $input = trim($input);
  $input = strip_tags($input);
  $input = htmlspecialchars($input);
  $input = addslashes($input);
  return $input;
}

?>

