
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>mycokhan management system
</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Segoe UI, sans-serif;
}





.sidebar{
    width:270px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:linear-gradient(180deg,#111827,#1e293b);
    padding:20px;
    box-shadow:5px 0 20px rgba(0,0,0,0.4);
}



.logo{
    text-align:center;
    font-size:30px;
    font-weight:bold;
    margin-bottom:40px;
    color:#38bdf8;
    letter-spacing:2px;
}



.menu{
    list-style:none;
}

.menu li{
    margin:15px 0;
}

.menu li a{
    text-decoration:none;
    color:white;
    display:block;
    padding:15px 20px;
    border-radius:15px;
    transition:0.3s;
    font-size:18px;
    background:rgba(255,255,255,0.03);
}



.menu li a:hover{
    background:yellowgreen;
    transform:translateX(10px);
    box-shadow:0 5px 15px rgba(56,189,248,0.5);
}



.menu li a.active{
    background:hotpink;
    box-shadow:0 5px 15px green;
}



.main{
    margin-left:270px;
    padding:40px;
    width:100%;
}

.main h1{
    font-size:45px;
    margin-bottom:20px;
}

.card{
    background:#1e293b;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 20px rgba(0,0,0,0.3);
}

.card p{
    margin-top:15px;
    line-height:1.8;
    color:#cbd5e1;
}



@media(max-width:768px){

.sidebar{
    width:200px;
}

.main{
    margin-left:200px;
}

}

</style>
</head>

<body>



<div class="sidebar">

    <div class="logo">
        WELCOME 
    </div>

    <ul class="menu">
         <li>
            <a href="" class="active">
                HOME
            </a>
        </li>
        <!-- <li>
            <a href="admin.php" class="active">
                ADMIN
            </a>
        </li> -->

        <li>
            <a href="register.php" class="active">
                REGISTER
            </a>
        </li>

        <li>
            <a href="login.php" class="active">
                LOGIN
            </a>
        </li>

        <li>
            <a href="dashboard.php" class="active">
                DASHBOARD
            </a>
        </li>

        <!-- <li>
            <a href="index.php#trips" class="active">
                BOOK NOW
            </a>
        </li> -->

         <li>
            <a href="delete.html" class="active">
                DELETE ACCOUNT
            </a>
        </li> 

        <li>
            <a href="logout.php" class="active">
                LOGOUT
            </a>
        </li>

    </ul>

</div>



</body>
</html>
