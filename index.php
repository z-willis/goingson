<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link type='text/css' rel='stylesheet' href='style/style.css'/>
        <link type='text/css' rel='stylesheet' href='style/normalize.css'/>
        <title>Testing</title>

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <body>
        <ul class="top-nav-bar">
            <li class="tab-left">
                <span onclick="openNav()"><a><image src="images/drawer.png"></image></a></span>
            </li>
            <li class="tab-right">
                <input type="submit"/>
            </li>
            <li class="tab-right">
                <div>Password:</div>
                <input placeholder="********"/>
            </li>
            <li class="tab-right">
                <div>Username:</div>
                <input placeholder="JBob123"/>
            </li>
        </ul>
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="#" id="opener">Events</a>
            <a href="#">Services</a>
            <a href="#">Clients</a>
            <a href="#">Contact</a>
        </div>

        <div id="dialog" title="Events">
            <ul class="events">
                <li class="entry">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">Event 1</h3>
                    <p class="text">The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least.</p>
                </li>
                <li class="entry">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">Event 2</h3>
                    <p class="text">The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least.</p>
                </li>
                <li class="entry">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">Event 3</h3>
                    <p class="text">The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least.</p>
                </li>
                <li class="entry">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">Event 4</h3>
                    <p class="text">The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least.</p>
                </li>
                <li class="entry">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">Event 5</h3>
                    <p class="text">The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least.</p>
                </li>
            </ul>
        </div>
        <script>
            /* Set the width of the side navigation to 250px */
            function openNav() {
                document.getElementById("mySidenav").style.width = "250px";
            }

            /* Set the width of the side navigation to 0 */
            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
            }

            $( function() {
                $( "#dialog" ).dialog({
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 600,
                    width: 600
                });

                $( "#opener" ).on( "click", function() {
                    $( "#dialog" ).dialog( "open" );
                });
            } );
        </script>
    </body>
</html>