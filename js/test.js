// start ahmed's code for the options of the voting dialog
$(document).ready(function(){
    $( "#votingDialog" ).dialog({
                    autoOpen: false,
                    show: false,
                    hide: false,
                    resizable: false,
                    draggable: false,
                    modal: true,
                    buttons:{
                        "Yes": function(){
                            // add the code that will increase the votes of the specified event in the database
                            jQuery.ajax({
                                url: "inc_vote.php",
                                data:{
                                    "id": $( "#eventid" ).val()
                                },
                                type: "GET",
                                success:function(data){
                                    $( this ).dialog( "close" );
                                }
                            })
                            
                        },
                        "No": function(){
                            // add the code that will decrease the votes of the specified event in the database
                        },
                        "I am not there": function(){
                            $( this ).dialog( "close" );
                        }
                    }
                });
                // end ahmed's code for the options of the voting dialog
});         
    