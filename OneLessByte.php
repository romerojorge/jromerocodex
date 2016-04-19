<?php
require_once ('phpFastCache/phpFastCache.php');
use phpFastCache\CacheManager;

$config = array(
    "storage"   =>  "Files",
    "path" => sys_get_temp_dir()
);
CacheManager::setup($config);

if ($_REQUEST["user_name"] == "slackbot") {
    exit;
}

$chat_id = $_REQUEST["token"] . "_" . $_REQUEST["user_name"];
$chat_history = CacheManager::get($chat_id . "_history");
if (is_null($chat_history)) {
    $chat_history = array ();
}
$chat_context = CacheManager::get($chat_id . "_context");
if (is_null($chat_context)) {
    $chat_context = array ();
}

$human_says = $_REQUEST["text"];
$chat_history = array_push ($chat_history, array("ts"=>time(), "text"=>$human_says));

$bot_says = converse ($human_says, $chat_context, $chat_history);
CacheManager::set($chat_id . "_context", $chat_context, 600); // cache for 600 seconds

if (is_null($bot_says) || trim($bot_says)==false) {
    $bot_says = "Sorry, I cannot understand you!";
}
$chat_history = array_push ($chat_history, array("ts"=>time(), "text"=>$bot_says));
CacheManager::set($chat_id . "_history", $chat_history, 600); // cache for 600 seconds
?>
    {
    "text": "<?php echo htmlspecialchars($bot_says) ?>",
    "mrkdwn": true
    }
<?php
// INPUT PARAMS
//   $human is the human message to this bot
//   $context is an array that contains any data related to this chat session. You can put any data here and access it later.
//   $history is an array of the chat history.

// RETURNS the bot's next message to the human

function converse ($human, &$context, $history) {


    switch ($human){

        case ("hello");
            return "Welcome to Onelessbyte\nHere are a few key words to get you started\n*new : If you are a new user\n*eat : To input your calorie intake";
            break;
        case ("*new");

            if (is_null($context['status'])) {
                $context['status'] = "wait_for_name";
                return "Lets begin by setting up your profile. Please enter _firstname lastname_";
            }

            // The user responded to the bot's question about name
            if ($context['status'] == "wait_for_name") {
                $context['name'] = $human;
                $context['status'] = "wait_for_height";
                return "Hello _" . $human . "_. please enter your Height in inches (ie 5ft = 60 inches.?";
            }

            // The user responded to the bot's question about birth year. We generate a response and set the status to NULL again so that we can start over.
            if ($context['status'] == "wait_for_height") {
                $context['height'] = $human;
                if ($human == 0) {
                    return NULL;
                }
                $context['status'] = "wait_for_weight";
                return " thank you, I have recorded your height to be " . $human. ", Now please enter your weight";
            }
            if ($context['status'] == "wait_for_weight") {
                $context['weight'] = $human;
                if ($human == 0) {
                    return NULL;
                }

                $context['status'] = "BMR?";
                $bmi = floatval((($context['weight'] * 703)/ ($context ['height'] * $context['height'])));
                return " Your current Bmi is ".$bmi. " would you like to calculate your BMR?";




            }
            if ($context['status'] == "BMR?"){
                $context['status'] = "age?";
                if ($human  == "no"){
                    return NULL;
                }elseif($human == "yes"|"y") {
                    return "please enter your age";
                }

            }
            // crashing after age.  check context status  and context age variable.


            if ($context['status'] == "age?"){
                $context['age'] = $human;
                if ($human == null){
                    return NULL;

                }
                $context['status'] = "sex?";
                return "Okay, I have recorded your age to be " .$human. " now please enter your gender ( male or female )" ;


            }
            if ($context['status'] == "sex?"){
                $context['sex'] = $human;
                if($human == "male"){
                    $context['status'] = "maleBMR";
                }
                if($human == "female"){
                    $context['status'] = "femaleBMR";

                }elseif ($human == null){
                    return NULL;

                }

            }
            if ($context['status'] == "maleBMR"){
                $BMR = 66 + (6.3 * floatval($context['weight'])) + (12.7 * floatval($context['height'])) - (4.8* floatval($context['age']));
                $context['status'] = "profile";
                return " Your BMR is " .$BMR. " your profile is ready \nWould you like to view your entire profile?";


            }
            if ($context['status'] == "profile"){
                if ($human  == "no"){
                    return NULL;
                }
                $context['status'] = "newconversation";
                return "Name: " .$context['name']. "\nHeight:".$context['height']."\nWeight: ".$context['weight']."\nAge: ".$context['age']."\nGender: ".$context['sex']." ";


            }

         




            break;

        case ("*eat");
            $context['status']="caloriecount";

            if ($context['status'] == "caloriecount") {
                $context['status'] = "bcalories?";
                return "please enter the number of calories you consumed for breakfast";

            }
            if ($context['status'] == "bcalories?") {
                $context['bcalories'] = $human;
                $context['status'] = "newconversation" ;
                return "Cool, I recorded your calories for breakfast";
            }
            if ($context['status'] == "newconversation"){

                exit;

            }




            break;


    }






}

?>