<?php

// $name="james";
// echo 'hello $name';

// $balance=5200;
// $name="james";
// $city="nakuru";

// if($name == "jamesf" || $city == "nakuruf"){
//     echo "you have enough money to buy pizza";
// } else {
//     echo "too broke to buy";
// }

// $weather = "sunny";

// if ($weather === "sunny") {
//   echo "Have a good morning!";
// } elseif ($t < "20") {
//   echo "Have a good day!";
// } else {
//   echo "Have a good night!";
// }

// $birthYear = 2000;
 
// function add($num1, $num2){
//     $sum = $num1 + $num2;
//     echo $sum . "<br>";
// }
// add(400,200)
// $balance=5000;
// function withdraw($amount){
//     global $balance;
//     $amount=2000;
//    $initialBalance = $balance;
//    $balance = $initialBalance - $amount;
//     echo "you have withdrawn $amount and your new balance is $balance";
// }
// echo withdraw(2000);

// $number=0;
// while($number <= 10){
//     echo $number . "<br>";
//     $number++;
// }

// $number=0;
// do {
//     echo $number . "<br/>";
//     $number++;
// }while($number <=20);

// for($number=0; $number <= 10; $number++){
//     echo $number . "<br/>";
// }

// $fruits = ["mangoes","oranges","melons","apples"];
// foreach ($fruits as $fruit){
//     echo $fruit ."<br/>";
// }

?>
<?php
$users =[
"John" =>1000,
"Jane" =>2000,
"Jim" =>3000,
"Jill" =>4000,
];
if ($_SERVER["REQUEST_METHOD"]=="GET" && isset($_GET['username']) && isset($_GET['amount'])) {
    $username = htmlspecialchars($_GET['username']);
    $amount = (int)$_GET['amount'];

    if (array_key_exists($username, $users)) {
        $maxLoan = $users[$username];
        if ($amount <= $maxLoan) {
            $message = "Loan approved for $username for amount $amount";
        } else {
            $message = "Loan denied for $username. Maximum allowable loan is $maxLoan";
        }
    } else {
        $message = "User $username not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Loan application</h1>
    <form action="" method="get">
        <label for="username">Name</label>
        <input type="text" name="username" id="username" required/>
        <br>
        <br>
        <label for="amount">Amount</label>
        <input type="text" name="amount" id="amount" required/>
        <br>
        <br>
        <button type="submit">Apply for loan</button>
    </form>
    <?php
    if (isset($message)) {
        echo "<h2>$message</h2>";
    }
    ?>
</body>
</html>