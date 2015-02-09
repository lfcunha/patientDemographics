/**
 * Created by luiscunha on 8/21/14.
 */


function act(elem){

    return 1;  //dont do anything for now. Later fix this implementation to validate in real time

    var value= elem.value;
    var name = $(elem).attr("name");

        var name_=name.split("X");
        console.log(name_);

        var id= $(elem).parent().attr("id");

    console.log(value, name_[1], id);

    $.ajax({
        type: "POST",
        url: "saveEditToDB",
        crossDomain: true,
        //data: {data:1},
        data: {value:value, col:name_[1], id:id, table:"extracts"},
        success: function(response) {
            console.log(JSON.stringify(response));

            if (response==1){

                //ajax call to a function to update database
                $(elem).parent().html(elem.value);
                $(elem).parent().css("border", "solid 1px #0f0");

            }
            else if (response==2) {
                //database error
            }
            else{
                //failed to pass validation
                $(elem).css("border-color", "#f00");
            }

        } ,
        error: function(request, status, error) {
            console.error(error);
            console.log('error');
            // alert('Not working!');
        },
        dataType: "text json"
    });
};

$(document).ready(function(){

    //$("div").delegate("#Sample_Identifier", "change", function(){
    //   if($("#quantityExtracts").val()=="")$("#quantityExtracts").val(1);
    //    $("#button3").attr("disabled", false)
    //});

    function checkIsolateIDExists(){


        a=$("*#idHandler").each(function(e){$(e)});

        for (var i=0; i<a.length; i++) {
            var sid=a[i].value;

            $.ajax({
                type: "POST",
                url: "checkIsolateIDExists",
                crossDomain: true,
                //data: {data:1},
                data: {data:sid},

                success: function(response) {
                    console.log("resp: " + JSON.stringify(response));

                    if (response=="1"){

                        return 1;

                    }
                    else{
                        //CIPqp4z-4qadzuc47-111wqqaxs52
                        //failed to pass validation
                        $(a[i]).attr("type", "text");
                        $(a[i]).css("width", "100%");
                        $(a[i]).attr("class", "fix");
                        //$("#idValue").text(null);
                        return 0;
                    }

                },
                error: function(request, status, error) {
                    console.error(error);
                    console.log('error');
                    // alert('Not working!');
                },
                dataType: "text json"
            });
        }

    }




y='</br><div class="form-group">  <label for="Sample_Identifier"></label> <input type="email" class="form-control" name="Sample_Identifier" id="Sample_Identifier" placeholder="Sample Identifier">   <label for="quantityExtracts"></label>  <input type="number" class="form-control" id="quantityExtracts" placeholder="#Extracts" name="quantityExtracts" min="1" max="50" style="min-width:100px"></br> ';


$("#button2").on("click",function (event) {
    event.preventDefault();  //prevent submit form
    $('.form-group').last().after(y);
    return false;
});


$("#button3").on("click", function (event) {
    event.preventDefault();  //prevent submit form
    console.log("button3 clicked: generate form");


    var count=0
    var isolateList=$('.form-group');
    _.each(isolateList, function(o){
        //console.log(isolateList.length)
        var sampleIdentifier = o.children[1].value;
        var numberExtracts = o.children[3].value;


        for (var i=0; i<numberExtracts; i++){

            s='<tr>';
            s=s+'<td><input type="hidden" row='+ i +' name="'+count+'XSample_Identifier" id="idHandler" value='+ sampleIdentifier + ' onblur="act(this)" /><span id="idValue">'+sampleIdentifier+'</span></td>';
            s=s+'<td><input type="text" row='+ i +' name="'+count+'XSample_Volume" id="vol" style="width:100%" onblur="act(this)"></td>';
            s+='<td><input type=text" row='+ i +' name="'+count+'XSample_Concentration(ng_ul)" id="conc" style="width:100%" onblur="act(this)"></td>';
            s+='<td><select name="'+count+'XType" row='+ i +' class="form-control" onblur="act(this)"><option value="">select</option><option value="qbit">Qbit</option><option value="nanodrop">Nanodrop</option><option value="other">Other</option></select></td>';

            s+='<td><div class="radio">' +
                '<label><input type="radio" row='+ i +' name="'+count+'XChimeric" id="chimericYes" value="yes">yes&nbsp;&nbsp;</label>' +
                '<label><input type="radio" row='+ i +' name="'+count+'XChimeric" id="chimericNo" value="no" checked>no</label>' +
                '</td>';

            s+='<td><select row='+ i +' name="'+count+'XCost_Site" class="form-control" onblur="act(this)"><option value="">select</option><option value="crip">CRIP</option><option value="invoice">Invoice</option></select></td>';

            s+='</tr>'

            $('div#table table tbody').append(s);
            count++;
        }
        count++;


        return false;
    });
    //var exists = checkIsolateIDExists();   Real time validation. For now just do validation on submit. later come back to it
    $("#form").hide();
    $("#table").show();


})

    $("#reset").on("click",function (event) {
        event.preventDefault();  //prevent submit form
        window.location="addExtracts";
        return false;
    });


})

