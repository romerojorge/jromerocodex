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
    $bot_says = "blah blah, please say something I actually understand!";
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



    if (is_null($context['status'])) {
        $context['status'] = "wait_for_name";
        return " WELCOME, Lets begin by setting up your 1-Byte profile. Please enter your First and Last Name";
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
        $context['status'] = "age?";
        return " I have recorded your weight to be ".$human. " please enter your age";


    }
    if ($context['status'] == "age?") {
        $context['age'] = $human;
        if ($human == 0) {
            return NULL;

        }
        $context['status'] = "sex?";
        return " I have recorded your age to be " . $human . " please enter your sex ( ie M for Male and F for Female";


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

    if ($context['status'] == "maleBMR") {

        // calculations before showing entire profile
        $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7* floatval($context['age']));
        $BMI = floatval((($context['weight'] * 703)/ ($context ['height'] * $context['height'])));

        $context['bmi']=$BMI;
        $context ['bmr']=$BMR;
        if ($BMI <= 18.4) {
            $context['bmiStatus'] = "Underweight";
        }
        if ($BMI >= 18.5 && $BMI <= 24.9) {
            $context['bmiStatus'] = "Healthy";
        }
        if ($BMI >= 25.0 && $BMI <= 29.9) {
            $context['bmiStatus'] = "overweight";
        }
        if ($BMI >= 30.0) {
            $context['bmiStatus'] = "Underweight";
        }

        $context['status'] = "Activity";

    }



        if ($context['status'] == "femaleBMR"){
            // calculations before showing entire profile
            $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7* floatval($context['age']));
            $BMI = floatval((($context['weight'] * 703)/ ($context ['height'] * $context['height'])));

            $context['bmi']=$BMI;
            $context ['bmr']=$BMR;
            if ($BMI <= 18.4) {
                $context['bmiStatus'] = "Underweight";
            }
            if ($BMI >= 18.5 && $BMI <= 24.9) {
                $context['bmiStatus'] = "Healthy";
            }
            if ($BMI >= 25.0 && $BMI <= 29.9) {
                $context['bmiStatus'] = "overweight";
            }
            if ($BMI >= 30.0) {
                $context['bmiStatus'] = "Underweight";
            }


            $context['status'] = "profile";





        }
    if ($context['status'] == "femaleBMR"){
        // calculations before showing entire profile
        $BMR = 655 + (4.35 * floatval($context['weight'])) + (4.7 * floatval($context['height'])) - (4.7* floatval($context['age']));
        $BMI = floatval((($context['weight'] * 703)/ ($context ['height'] * $context['height'])));

        $context['bmi']=$BMI;
        $context ['bmr']=$BMR;
         if ($BMI <= 18.4) {
             $context['bmiStatus'] = "Underweight";
         }
        if ($BMI >= 18.5 && $BMI <= 24.9) {
            $context['bmiStatus'] = "Healthy";
        }
        if ($BMI >= 25.0 && $BMI <= 29.9) {
            $context['bmiStatus'] = "overweight";
        }
        if ($BMI >= 30.0) {
            $context['bmiStatus'] = "Underweight";
        }







    }

    if ($context['status'] == "profile"){
        $context['status'] = "NA";
        return "Name: " .$context['name']. "\nHeight:".$context['height']."\nWeight: ".$context['weight']."\nAge: ".$context['age']."\nGender: ".$context['sex']."\n BMI: " .$context['bmi']. ":" .$context['bmiStatus']. "\n BMR : ". $context['bmr']. ": This is the amount of calories you need to consume in order to maintain your current weight";



    }
// conversation using free words
    if ($context['status'] == "NA") {
// calorie counter after eating
        if ((strpos($human, 'just ate') !== false) or (strpos($human, 'calories') ) !== false) {
            $context['status'] = "calorie_status";
            return "how many calories did you consume";
// random test word
}
        if (strpos($human, 'bitch') !== false) {
            $context['status'] = "calorie_s";
// show your profile again

        }
        if (strpos($human, 'profile') !== false) {
            $context['status'] = "profile";


        }

        // Template for burning calories - follow line 261


        if ((strpos($human, 'burn') !== false) and (strpos($human, '500')) !== false and (strpos($human, 'calories') !== false)) {
            $context['status'] = "500calwork";


        }



//


    }
     if ($context['status'] == "calorie_status"){

         $context ['status'] = "NA";

         $context ['calories'] = $human;
         $context['calorie_counter'] += $context['calories'];

         if ($context ['calorie_counter']>= $context['bmr']){
             $context ['calorie_status'] = " Wow Slow down there buddy";
         }else{
             $context ['calorie_status'] = " ";
         }


         
         return " you have ".$context['calorie_counter']. " ".$context['calorie_status'];
     }

    if ($context['status'] == "calorie_s"){
        $context ['status'] = "NA";
return "fuck off";


    }

    if ($context['status'] == "500calwork"){

        $context ['status'] = "NA";
        return "Tennis (520 calories)\n
Running (600 calories)\n
Bicycling (600 calories)\n
Football (600 calories)\n
Basketball (600 calories)\n
Soccer (600 calories)\n
all 1 HOUR ";


    }




}

?>