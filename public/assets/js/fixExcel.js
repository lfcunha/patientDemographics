/**
 * Created by luiscunha on 8/21/14.
 */

$(window).keydown(function(event){
    if(event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
})



var rows;
function addtodatabase(val1,val2) {

    function go(id){

        $.ajax({
            type: "POST",
            url: "addToAuxiliaryDatabase",
            crossDomain: true,
            data: {column:val2, value:$(id).val()},

            success: function(response) {

                console.log(response);
                function updateStyle(){
                    $(id).css("border-color", "#0f0");
                    //$(id).attr("disabled", "disabled");
                    $('.dbicon').popover("hide");
                    $(id).attr("class", "fixed");
                    var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";
                    //console.log(sel_fa);
                    $(sel_fa).css("color","#000");
                    $(id).css("width", "100%");
                    $(sel_fa).hide();

                    //changeStylingOfSimilarEntries
                    (function(){
                        for (var i=0; i<rows; i++){
                            var id="#"+i+"_"+val2;
                            if (valuefordb==$(id).val()){
                                $(id).css("border-color", "#0f0");
                                $(id).attr("class", "fixed");
                                var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";
                                $(sel_fa).css("color","#000");
                                $(id).css("width", "100%");
                                $(sel_fa).hide();
                            }
                        }
                    })()
                }
                if (response==1) {
                    updateStyle();
                }
                else alert('failed to add to database. If problem persists, please contact us!');
                //alert(response);
            },
            error: function(request, status, error) {
                console.error(error);
                console.log('error');
                alert('failed to add to database. If problem persists, please contact us!');
            },
            dataType: "text json"
        });
    }
    var id="#"+val1+"_"+val2;
    valuefordb=$(id).val();
    go(id);
};




function validate(val, table){
    console.log("validate");
    var id="#"+val;
    var col=val.split("_");
    var table=table;
 /*   if(col[1]=="2"){    //check if it's column3 (non-duplicated Sample Identifiers)
        var value=$(id).val();
        if(sids.indexOf(value)>-1) return;   //if it's a duplicated value, exit. do not do server validation
        sids.push(value);
    }*/
    $.support.cors = true;

    $.ajax({
        type: "POST",
        url: "validate2",
        crossDomain: true,
        //data: {data:1},
        data: {cell:val,data:$(id).val(), row:val.split("_")[0], col:val.split("_")[1], table:table},
        beforeSend: function(){
            console.log(this.data);
        },
        success: function(response) {
            console.log(JSON.stringify(response));
            if (response==1){
                //$(id).attr("disabled", "disabled");
                $(id).attr("class", "fixed btn");
                $(id).tooltip('destroy');
                //$(id).blur();

                var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";
                $(sel_fa).css("display", "none");

                var a =$(".fix").length<1;
                if(a){
                    $("#submitbutton").removeAttr('disabled');
                    $("#submitbutton").attr("class", "btn btn-primary");
                }
                console.log(a);
            }
            else {
                $(id).attr("class", "fix btn");
                $(id).tooltip('show');
                var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";
                $(sel_fa).css("display", "block");
            }

            //alert(response);
        },
        error: function(request, status, error) {
            console.error(error);
            console.log('error');
            // alert('Not working!');
        },
        dataType: "text json"
    });

}
//array to hold sample identifiers (to check for duplicates)
var sids=new Array();
$( document ).ready(function() {
    $('.notdb').tooltip({
        trigger: 'hover focus',
        html:true
    });

    $('.db').tooltip({
        trigger: 'hover focus',
        html:true
    });

    $('.dbicon').popover({
        trigger: 'click',
        html: true
    });


});

//build array of sample identifiers, to check for duplicates in the validate function






function closePopover(value){
    var id="#"+value;

    var sel_fa="i"+id+".fa.fa-database.fa-1.dbicon";

    $(sel_fa).popover("hide");
};

function redirect(path){
    window.location.replace("/databases/influenza/" + path);
};



(function(){
    if(typeof(rows)!=undefined){
        for (var i=0; i<rows; i++){
            var id="#"+i+"_"+3;
            var sidsValue=$(id).val();
            if (sids.indexOf(sidsValue)<0){
                sids.push($(id).val())
            }
        }}})();




