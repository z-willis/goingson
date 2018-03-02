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
        <ul class="nav-bar">
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

        <div id="dialog" title="Basic dialog">
            <p>This is an animated dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.</p>
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
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    hide: {
                        effect: "explode",
                        duration: 1000
                    }
                });

                $( "#opener" ).on( "click", function() {
                    $( "#dialog" ).dialog( "open" );
                });
            } );
        </script>
    </body>
</html>