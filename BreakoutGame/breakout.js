var dx, dy;       /* displacement at every dt */
var x, y;         /* ball location */
var score = 0;    /* # of walls you have cleaned */
var tries = 0;    /* # of tries to clean the wall */
var started = false;  /* false means ready to kick the ball */
var ball, court, paddle, brick, msg;
var court_height, court_width, paddle_left;
var ballTimer;
var bricks = new Array(4);  // rows of bricks
var colors = ["red", "blue", "yellow", "green"];
var totalHits = 0; //total hit count for bricks
var dX,dY; // speed variable for x and y direction

/* get an element by id */
function id(s) { return document.getElementById(s); }

/* convert a string with px to an integer, eg "30px" -> 30 */
function pixels(pix) {
    pix = pix.replace("px", "");
    num = Number(pix);
    return num;
}

/* place the ball on top of the paddle */
function readyToKick() {
    x = pixels(paddle.style.left) + paddle.width / 2.0 - ball.width / 2.0;
    //console.log(x);
    y = pixels(paddle.style.top) - 2 * ball.height;
    //console.log(y);
    ball.style.left = x + "px";
    ball.style.top = y + "px";
}

/* paddle follows the mouse movement left-right */
function movePaddle(e) {
    //console.log(e.pageX)
    var ox = e.pageX - court.getBoundingClientRect().left;
    paddle.style.left = (ox < 0) ? "0px"
        : ((ox > court_width - paddle.width)
            ? court_width - paddle.width + "px"
            : ox + "px");
    if (!started)
        readyToKick();
}

function initialize() {
    court = id("court");
    ball = id("ball");
    paddle = id("paddle");
    wall = id("wall");
    msg = id("messages");
    brick = id("red");
    court_height = pixels(court.style.height);
    court_width = pixels(court.style.width);
    for (i = 0; i < 4; i++) {
        // each row has 20 bricks
        bricks[i] = new Array(20);
        var b = id(colors[i]);
        for (j = 0; j < 20; j++) {
            var x = b.cloneNode(true);
            x.id  = colors[i]+j; // setting the id of each brick uniquely 
            bricks[i][j] = x;
            wall.appendChild(x);
        }
        b.style.visibility = "hidden";
    }
    started = false;
}

/* true if the ball at (x,y) hits the brick[i][j] */
function hits_a_brick(x, y, i, j) {
    var top = i * brick.height - 420;
    var left = j * brick.width;
    return (x >= left && x <= left + brick.width
        && y >= top && y <= top + brick.height);
}


function startGame() {
    //start the game if not already started 
    if(!started ){
        started = true;
        var speed = getSelectedSpeed();
        var varX = getRandomAngle();
        dX = varX*speed;
        //console.log(varX,dX);
        dY = -1*speed;
        ballTimer = window.setInterval(moveBall, 5) 
    }
}

function resetGame() {
    //restore bricks
    for (i = 0; i < 4; i++) {
        // each row has 20 bricks
        for (j = 0; j < 20; j++) {
            id(colors[i]+j).style.visibility="visible"
        }
    }
    //reset tries
    var triesElem = id("tries")
    tries = 0;
    triesElem.textContent = tries;
    //reset hit bricks count
    totalHits = 0;
}

function moveBall(){
    
    var tryAgainFlag = false;

    var ballPositionX = pixels(ball.style.left); // get current ball position on x direction
    var ballPositionY = pixels(ball.style.top); //  get current ball position on y direction

    var upperBoundary = court_height - ball.height; // upper boundary of court 
    var lowerBoundary = pixels(paddle.style.top) + ball.height; // lower boundary of court

    var rightBoundary = court_width - ball.width; // right boundary of court
    var leftBoundary = 0; // left boundary of court
    
    var paddlePosition = pixels(paddle.style.left); // get current paddle poisition
    
    //check if ball hits any brick(s)
    for (i = 0; i < 4; i++) {
        // each row has 20 bricks
        for (j = 0; j < 20; j++) {

            // flag to identify if brick hit occur
            // , will be used to change the direction of ball if ball hits a brick
            var flag = false;

            //check if ball hits a brick at i, j for direction at ballPositionX and ballPositionY
             if (hits_a_brick(ballPositionX, ballPositionY, i, j) && id(colors[i]+j).style.visibility != "hidden"){
                var brick_id = id(colors[i]+j);
                brick_id.style.visibility = "hidden"; // hide the brick 
                flag = true; 
                totalHits+=1; //increase the brick hit counter
             }

             // check if ball hits next brick as well
             // as ball width is 20 pixels so it is possible to hit two bricks 
             // so checking the next brick as well
             if (hits_a_brick(ballPositionX+20, ballPositionY, i, j+1) && j+1 <=19){
                if (id(colors[i]+(j+1)).style.visibility != "hidden"){
                    var brick_id = id(colors[i]+(j+1)); // get next brick id 
                    brick_id.style.visibility = "hidden";// hide the brick 
                    flag = true; 
                    totalHits+=1; //increase the brick hit counter
                }
            }

            //if brick hit detected, then change the ball direction
            if (flag){
                dY = -dY;
            }
        }       
    }

    //check upper boundary hit
    if(Math.abs(ballPositionY) >=  upperBoundary && dY <0){
        dY = -dY; //change ball direction on y axis
    }
    //check lower boundary hit
    if(Math.abs(ballPositionY) <=  lowerBoundary && dY >0){
        //check if paddle hit
        if (Math.abs(ballPositionX) <= paddlePosition + paddle.width
                && Math.abs(ballPositionX) >= paddlePosition) {
                dY = -dY; //change ball direction on y axis
        }
        //else increase tries and set ball on paddle
        else{
            //set the tryAgainFlag to true if ball missed by paddle
            tryAgainFlag = true;   
        }
    }
    
    //check right boundary hit
    if(ballPositionX >=  rightBoundary && dX >0){
        dX = -dX;//change ball direction on x axis
    }
    
    //check left boundary hit
    if(ballPositionX <=  leftBoundary && dX <0){
        dX = -dX;//change ball direction on x axis
    }

    //update ball position by dY, dX in y, x directions
    ball.style.top = ballPositionY + dY + "px";
    ball.style.left = ballPositionX + dX + "px";

    //check if all bricks destroyed
    //then increase score and reset the game
    if(totalHits == 80){
        scoreAndReset();
        stopTimer();
    }

    //if user is not able to bounce the ball from paddle 
    //then increase tries
    if (tryAgainFlag){
        increaseTries();
        stopTimer();
    }
}

//stop set interval
function stopTimer(){
    clearInterval(ballTimer);
    ballTimer = undefined;
}

//Function for random angle between 45 and 135
function getRandomAngle() {
    var min = -1;
    var max = 1;
    return Math.round((Math.random() * (max - min ) + min) * 100) / 100
}

//increase score and reset the game
function scoreAndReset(){
    score+=1;
    var scoreElem = id("score")
    scoreElem.textContent = score;
    started = false;
    readyToKick();
    resetGame();
}

//increase tries and place ball on paddle
function increaseTries() {
    triesElem = id("tries")
    tries += 1;
    triesElem.textContent = tries;
    started = false;
    readyToKick();
}

//get level value
function getSelectedSpeed(){
    var level = id("level");
    return level.value;
}