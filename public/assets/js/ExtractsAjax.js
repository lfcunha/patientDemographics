/**
 * Created by luis on 9/2/14.
 */

$(document).ready(function(){
    $('#quickSearch').on('keypress', function(e) {
        if(e.keyCode==13){
            quickSearch();
        }
    });

    $('td').delegate('#editField', 'keypress', function(e) {
        if(e.keyCode==13){
            act(this);
            console.log(2);
        }
    });

    //Reset the search when click "clear" button inside search box
    $('#quickSearch').on("click", function(){
        setTimeout(function(){eraseSearch()}, 100);
    })

    $('th.sorting').on("click", function(){
        sortByColumn(this);
    });

    //clear the checkboxes if the user pushes the backbutton
    $(":checkbox").prop('checked', false);




    setButtons();
}); //doc.ready


function eraseSearch(){
    console.log(3);
    if( $('#quickSearch').val().length<1){
        $("#faAvancedSearch").click();
    }
}

var checkedRowsSet=new Array();
function checkedRows(elem){
    self=elem;
    var index = checkedRowsSet.indexOf(self.id);
    if(self.checked) {
        if (index<0) {
            checkedRowsSet.push(self.id)

        }}
    else {
        if (index>-1) checkedRowsSet.splice(index, 1);
        if($("#all").is(":checked")) $("#all").prop("checked", false);
    }
    var values_ = checkedRowsSet.join();
    $("#submitExportSampleIds").val(values_);
    $("#submitAssembliesSampleIds").val(values_);
}
var all=0;
function activateTableSelections(){


    if (all==0){
        //this is the first time "all" is clicked, so do add all to the checkedRowsSet array
        $(":checkbox").parent().parent().children().find(":checkbox").prop('checked', true);

        if($("#all").is(":checked")) {
            $(":checkbox").prop('checked', true)
            $(":checkbox").each(function(e,v){
                var index = checkedRowsSet.indexOf(v.id);
                if (index<0) {
                    checkedRowsSet.push(v.id)
                }
            })

            var values_ = checkedRowsSet.join();
            $("#submitExportSampleIds").val(checkedRowsSet.join());
            $("#submitAssembliesSampleIds").val(checkedRowsSet.join());
        }
        //add a listener for future clicks
        $("#all").on("click", function(){
            if($("#all").is(":checked")) {
                $(":checkbox").prop('checked', true)
                $(":checkbox").each(function(e,v){

                    var index = checkedRowsSet.indexOf(v.id);

                    if (index<0) {
                        checkedRowsSet.push(v.id)
                    }
                })
                $("#submitExportSampleIds").val(checkedRowsSet.join());
                $("#submitAssembliesSampleIds").val(checkedRowsSet.join());
            }
            else {
                //$(":checkbox").parent().parent().children().find(".checkbox").prop('checked', false)
                $(":checkbox").prop('checked', false)
                $(":checkbox").each(function(e,v){

                    var index = checkedRowsSet.indexOf(v.id);
                    if (index>-1) checkedRowsSet.splice(index, 1);
                })
                $("#submitExportSampleIds").val(checkedRowsSet.join());
                $("#submitAssembliesSampleIds").val(checkedRowsSet.join());
            }
        });

    }
    all=1;
}



function setButtons(){

    if (totalRecords>7){
        $('.next').removeClass("disabled");
    }
    if(pages==1){
        $('.previous').addClass('disabled');
        $('.next').addClass('disabled');
    }
    var elipsis0_='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    var elipsis1_='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    var elipsis1 = $($.parseHTML(elipsis1_));
    var elipsis0 = $($.parseHTML(elipsis0_));
    var first   = '<li class="paginate_button " aria-controls="isolatesTable" tabindex="1" onclick="select(this);return 0;"><a href="#">1</a></li>';
    var first_  =  $($.parseHTML(first));
    var second_ = '<li class="paginate_button " aria-controls="isolatesTable" tabindex="2" onclick="select(this);return 0;"><a href="#">2</a></li>';
    var second  =  $($.parseHTML(second_));


    //'NEXT' BUTTON
    $('.next').on('click', function(){
        if ( $('.next').attr("class").indexOf("disabled")>-1){
            // console.log("done");
            return 0; //if end of list, don't respond to click
        }

        if ( $('.previous').attr("class").indexOf("disabled")>-1){
            $('.previous').removeClass('disabled');
        }

        var active=$(".active");

        if ($(active).attr('tabindex')==pages-1) {
            $('.next').addClass("disabled"); //if at end of list, disable this "next" button
        }
        var a;
        var elipsis0;
        var next;
        if (pages>2){ //must handle case of pages=2 differently. see bellow
            if ($(active).attr('tabindex')<5 || $($('*.pagination').children().get(3)).attr('tabindex')>=pages-4) {
                //console.log("5_1");
                if ($($('*.pagination').children().get(3)).attr('tabindex')>=pages-4) {
                    // console.log("pages-4");
                    var secondToLast=$(first_).attr('tabindex', pages-1 );
                    $(secondToLast).children().html(pages-1);
                    a = $('*.pagination').children().get(-3);
                    $(a).replaceWith(secondToLast);
                }
                next = $(active).next();
                $(active).removeClass("active");
                $(next).addClass("active");
                getData($(next),0);
            }


            else if ($(active).attr('tabindex')>=5 && $($('*.pagination').children().get(3)).attr('tabindex')<pages-4 ) {
                //console.log("5_2");
                elipsis0 = $('#isolatesTable_ellipsis').clone(true);
                $(second).replaceWith(elipsis0);

                var firstElipsis = $('*.pagination').children().get(2);
                elipsis0='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
                var $elipsis0 = $($.parseHTML(elipsis0));

                $(firstElipsis).replaceWith($elipsis0.clone());

                a=$('.paginate_button');

                for (var i=3; i<a.length-3; i++){
                    $(a[i]).attr("tabindex", parseInt($(a[i]).attr("tabindex")) +1);
                    $(a[i]).children().html(parseInt($(a[i]).children().html())+1);
                }
                getData($(active),0);
            }
        }
        else if(pages==2){
            next = $(active).next();
            $(active).removeClass("active");

            $(next).addClass("active");
            getData($(next),0);

        }

    });

    //'PREVIOUS' BUTTON
    $('.previous').on('click', function(){
        var active;
        if ( $('.previous').attr("class").indexOf("disabled")>-1){
            // console.log("done");
            return 0;
        }

        if ( $('.next').attr("class").indexOf("disabled")>-1){
            $('.next').removeClass('disabled');
        }

        active=$(".active");

        if ($($('*.pagination').children().get(3)).attr('tabindex')<=3 || $(active).attr('tabindex')>pages-4) {
            //  console.log("5_1");

            if ($($('*.pagination').children().get(5)).attr('tabindex')<=5) {

                if ($($('*.pagination').children().get(2)).attr("tabindex")=="-3"){
                    //console.log("replace elipsis");
                    var first_=$($('*.pagination').children().get(1)).clone();
                    $(first_).attr("tabindex", 2);
                    $(first_).children().html(2);
                    var second = $('*.pagination').children().get(2);
                    //var elipsis0='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>'
                    $(second).replaceWith(first_);
                }
            }

            active=$(".active");
            var previous = $(active).prev();
            $(active).removeClass("active");
            $(previous).addClass("active");
            active=$(".active");
            getData($(active),0);
            if ($(active).attr('tabindex')==1) {
                $('.previous').addClass("disabled");
            }
        }

        else if ($($('*.pagination').children().get(3)).attr('tabindex')>3  && $($('*.pagination').children().get(3)).attr('tabindex')<=pages-4 ) {
            // console.log("5_2");
            var elipsis1='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
            var $elipsis1 = $($.parseHTML(elipsis1));
            var secondElipsis = $('*.pagination').children().get(6);
            $(secondElipsis).replaceWith($elipsis1.clone());

            var a=$('.paginate_button');
            for (var i=3; i<a.length-3; i++){
                $(a[i]).attr("tabindex", parseInt($(a[i]).attr("tabindex")) -1);
                $(a[i]).children().html(parseInt($(a[i]).children().html())-1);
            }
            getData($(active),0);
        }
    });
    $($('*.pagination').children().get(1)).addClass('active');
} //setbuttons





function tbodyIsolatesTemplate(data){
    //console.log(data);
    //console.log("1: " + pages);
    pages=Math.ceil(data.count/data.size);
    // console.log("2: " + pages);
    //console.log("total: " +data['total'] + "  /   " + "size: " + data['size'])

    if ($($('*.pagination').children().get(-2)).attr('tabindex')!=pages && pages>0){
        $($('*.pagination').children().get(-2)).attr('tabindex', pages);
        $($('*.pagination').children().get(-2)).children().html(pages);
    }

    //var sample=JSON.parse(data)['data']

    var tbody=$('table').find('tbody')[0];
    $(tbody).empty();

    //console.log(data.data);
    for(var x in data.data){
        var row = data.data[x];

        var tr= '<tr id="'+row.idExtract+'"></tr>';
        $(tbody).append(tr);

        var date;
        var formatedDate;
        if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1)
        {
            date = row.Timestamp;
            var date_=date.split(" ");
            //console.log(date_);
            formatedDate=date_[0];
        }
        else {
            date = new Date(row.Timestamp);
            formatedDate = (date.getMonth() + 1) + '/' + date.getDate() + '/' +  date.getFullYear();
        }

        var url=document.URL;
        var editStatus='ondblclick="$(this).html(&#39;<select onchange=&quot;setStatusMulti(this)&quot; onblur=&quot;act(this)&quot; class=&quot;form-control&quot;><option value=&quot;Data entered&quot;>Data entered</option><option value=&quot;Samples received&quot;>Samples received</option><option value=&quot;Library prep&quot;>Library prep</option><option value=&quot;Sequencing&quot;>Sequencing</option><option value=&quot;Run complete&quot;>Run complete</option><option value=&quot;Complete&quot;>Complete</option><option value=&quot;Partial&quot;>Partial</option><option value=&quot;Insufficient cDNA&quot;>Insufficient cDNA</option><option value=&quot;Failed QC&quot;>Failed QC</option><option value=&quot;Failed prep&quot;>Failed prep</option></select>&#39;); $(this).children().focus();$(this).children().css(&#39;width&#39;,$(this).width())"';
        var MeasumentType='ondblclick="$(this).html(&#39;<select onblur=&quot;act(this)&quot; class=&quot;form-control&quot;><option value=&quot;Qbit&quot;>Qbit</option><option value=&quot;Qbit&quot;>Qbit</option><option value=&quot;Nanodrop&quot;>Nanodrop</option><option value=&quot;Spec&quot;>Spec</option><option value=&quot;Other&quot;>Other</option></select>&#39;);$(this).children().focus();$(this).children().css(&#39;width&#39;,$(this).width())"';
        var ChimericSegments='ondblclick="$(this).html(&#39;<select onblur=&quot;act(this)&quot; class=&quot;form-control&quot;><option value=&quot;Yes&quot;>Yes</option><option value=&quot;Yes&quot;>Yes</option><option value=&quot;No&quot;>No</option></select>&#39;); $(this).children().focus();$(this).children().css(&#39;width&#39;,$(this).width())"';
        var CostSite='ondblclick="$(this).html(&#39;<select onblur=&quot;act(this)&quot; class=&quot;form-control&quot;><option value=&quot;crip&quot;>crip</option><option value=&quot;CRIP&quot;>CRIP</option><option value=&quot;Invoice&quot;>Invoice</option></select>&#39;);$(this).children().focus();$(this).children().css(&#39;width&#39;,$(this).width())"';


            var td='<td ><input id="'+ row.idExtract +'" class="checkbox" type="checkbox" onchange="checkedRows(this)"></td>';
            td=td+ '<td id="'+ row.idExtract +'"                   name="Extract Identifier" >'+row.idExtract+'</td>';
        if (row.admin==1) {
            td=td+ '<td id="'+ row.idExtract +'" class="crosshair" value="'+ row.status  +'" name="Status"'+ editStatus   + ' >'+row.Status +'</td>';
            td=td+ '<td id="'+ row.idExtract +'" name="Sample Identifier" >'+row.Sample_Identifier +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Project Identifier" style="min-width:250px: max-width:300px" >'+row.Project_Identifier+'</td>';
            td=td+ '<td id="'+ row.idExtract +'" name="Strain Name" >'+row.Strain_Name +'</td>';
            td=td+ '<td id="'+ row.idExtract +'" class="crosshair" name="Sample Volume" ondblclick="generateEditInput(this,&#39;'+row.Sample_Volume +'&#39;)">'+row.Sample_Volume +'</td>';
            td=td+ '<td id="'+ row.idExtract +'" class="crosshair" name="Sample Concentration(ng_ul)" ondblclick="generateEditInput(this,&#39;'+row.concentration +'&#39;)">'+row.concentration +'</td>';

            td=td+ '<td id="'+ row.idExtract +'" class="crosshair" name="Measurement Type" '+  MeasumentType   +'>'+row.Measument_Type +'</td>';
            td=td+ '<td id="'+ row.idExtract +'" class="crosshair" name="Chimeric Segments"  '+   ChimericSegments   +'   >'+row.Chimeric_Segments +'</td>';
            td=td+ '<td id="'+ row.idExtract +'" class="crosshair" name="Cost Site" '+   CostSite   +' >'+row.Cost_Site +'</td>';

            td=td+ '<td id="'+ row.idExtract +'"  name="Timestamp" ondblclick="generateEditInput(this,&#39;'+formatedDate +'&#39;)">'+ formatedDate+'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="User" >'+row.username +'</td>';

        }
        else {
            td=td+ '<td id="'+ row.idExtract +'"  name="Status" >'+row.Status +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Sample Identifier">'+row.Sample_Identifier +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Project Identifier" style="min-width:250px: max-width:300px" >'+row.Project_Identifier+'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Strain Name">'+row.Strain_Name +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Sample Volume">'+row.Sample_Volume +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Sample Concentration(ng_ul)" >'+row.concentration +'</td>';

            td=td+ '<td id="'+ row.idExtract +'"  name="Measument Type" >'+row.Measument_Type +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Chimeric Segments" >'+row.Chimeric_Segments +'</td>';
            td=td+ '<td id="'+ row.idExtract +'"  name="Cost Site" >'+row.Cost_Site +'</td>';

            td=td+ '<td id="'+ row.idExtract +'"  name="Timestamp" >'+ formatedDate+'</td>';

        }





        $(tbody).find('tr').last().append(td);
    }
    var lowerlimit=parseInt(data.offset)+1;
    var upperlimit=parseInt(data.size)+ parseInt(data.offset);
    (upperlimit > data.count)?upperlimit = data.count:upperlimit;



    var info='Showing ' + lowerlimit + ' to ' + upperlimit +' of '+data.count+' entries (filtered from '+ data.total + ' total entries) ';
    $("#isolatesTable_info").html(info);

    setCheckboxes(); //function is in tableAjax.js
}


function repaginate(data) {
    pages=Math.ceil(data.total/data.size);
    // console.log("pages: " + pages);
    var second = '<li class="paginate_button" aria-controls="isolatesTable" tabindex="2" onclick="select(this);return 0;"><a href="#">2</a></li>';
    var $el = $($.parseHTML(second));
    var elipsis1='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    var $elipsis1=$($.parseHTML(elipsis1));
    var el;
    var previous;
    var next;

    if (pages>7){
        // console.log(">7");
        $($('*.pagination')).empty();
        previous='<li class="paginate_button previous disabled" aria-controls="isolatesTable" tabindex="-4" id="isolatesTable_previous"><a href="#">Previous</a></li>';
        next = '<li class="paginate_button next" aria-controls="isolatesTable" tabindex="-1" id="isolatesTable_next"> <a href="#">Next</a> </li>';
        $($('*.pagination')).append(next);
        el=$el.clone();
        el.attr("tabindex", pages);
        el.children().html(pages);
        $($('*.pagination')).prepend(el);
        var elipsis1_=$elipsis1.clone();
        $($('*.pagination')).prepend(elipsis1_);

        for (var i=5; i>0; i--){
            el=$el.clone();
            el.attr("tabindex", i);
            el.children().html(i);
            $($('*.pagination')).prepend(el);
        }

        $($('*.pagination')).prepend(previous);
        $($('*.pagination').children().get(1)).addClass("active");
        setButtons();

        $('.next').removeClass("disabled");
        $('.previous').addClass("disabled");
    }
    else {
        // console.log("<7");
        $($('*.pagination')).empty();
        previous='<li class="paginate_button previous disabled" aria-controls="isolatesTable" tabindex="-4" id="isolatesTable_previous"><a href="#">Previous</a></li>';
        next = '<li class="paginate_button next" aria-controls="isolatesTable" tabindex="-1" id="isolatesTable_next"> <a href="#">Next</a> </li>';

        $($('*.pagination')).append(next);
        for (var i=pages; i>0; i--){
            el=$el.clone();
            el.attr("tabindex", i);
            el.children().html(i);
            $($('*.pagination')).prepend(el);
        }
        $($('*.pagination')).prepend(previous);
        $($('*.pagination').children().get(1)).addClass("active");
        setButtons();
    }
    tbodyIsolatesTemplate(data);
}

//redo the pagination menu when changing the selection for the number of rows to display
function getRepaginationData(elem){
    var isolatesTable_length = $("#isolatesTable_length").children().children().val();
    var offset=0;
    var column;
    var order;
    $('#quickSearch').val("");

    if (elem){
        column=$(elem).html();
        switch(column) {
            case "Extract ID": column = "idExtract";
                break;
            case "Sample ID ":  column = "Sample_Identifier";
                break;
            case "Project ID":  column = "Project_Identifier";
                break;
            case "Vol(µl)":  column = "Sample_Volume";
                break;
            case "[ ](ng/µl)":  column = "Sample_Characterization(ng_ul)";
                break;
            case "Strain Name":  column = "Strain_Name";
                break;
            case "[ ] Type":  column = "Measument_Type";
                break;
            case "Chimeric":  column = "Chimeric_Segments";
                break;
            case "Cost Site":  column = "Cost_Site";
                break;
            case "Date/Time":  column = "Timestamp";
                break;
        }
        if (column.indexOf("Vol")>-1) column="Sample_Volume";
        if (column.indexOf("ng")>-1)  column="Sample_Concentration(ng_ul)";

        order=$(elem).attr("class").split(" ")[1];

        $($("th")[0]).attr("class","");
        $($("th")[12]).attr("class","");
    }
    else{
        if($(".sort").length > 0){
            getRepaginationData($(".sort"));
            return 0;
        }
        else {
            column = "Timestamp";
            order  = "sorting_desc";
        }
    }

    $.ajax({
        type: 'post',
        url: 'getExtractsAjax',
        data: {length:isolatesTable_length, offset:offset, column:column, order:order},
        //data: {id:"1"},
        beforeSend: function(){
           // console.log(isolatesTable_length, offset, column, order);
        },
        success: function (response) {
            var data=JSON.parse(response);
            repaginate(data);
        },
        error: function() {
            console.error("error");
        }
    });
}

function sortByColumn(elem){
    var order;
    if ($(elem).html()=="Select" || $(elem).html()=="Details") return 0;
    var sortClass=$(elem).attr("class");
    if (sortClass=="sorting" || sortClass == "sort sorting_desc") {
        order="sorting_asc";
        $("th").attr("class", "sorting");
        $(elem).attr("class", "sort sorting_asc");
    }
    else {
        order="sorting_desc";
        $("th").attr("class", "sorting");
        $(elem).attr("class", "sort sorting_desc");
    }
    var searchTerm = $("#quickSearch").val();
    if (searchTerm===""){
        getRepaginationData(elem);
    }
    else {
        //  console.log("sbc_qs")
        quickSearch(); // don't call it until implement the database search code
    }
}

function quickSearch(offset,source){
    var column;
    var order;
    // console.log("qs");
    //$("th").attr("class", "sorting");

    if ($(".sort").length>0){
        var elem=$(".sort");
        column=$(elem).html();
        switch(column) {
            case "Extract ID": column = "idExtract";
                break;
            case "Sample ID ":  column = "Sample_Identifier";
                break;
            case "Project ID":  column = "Project_Identifier";
                break;
            case "Vol(µl)":  column = "Sample_Volume";
                break;
            case "[ ](ng/µl)":  column = "Sample_Characterization(ng_ul)";
                break;
            case "Strain Name":  column = "Strain_Name";
                break;
            case "[ ] Type":  column = "Measument_Type";
                break;
            case "Chimeric":  column = "Chimeric_Segments";
                break;
            case "Cost Site":  column = "Cost_Site";
                break;
            case "Date/Time":  column = "Timestamp";
                break;
        }
        if (column.indexOf("Vol")>-1) column="Sample_Volume";
        if (column.indexOf("ng")>-1)  column="Sample_Concentration(ng_ul)";

        order=$(elem).attr("class").split(" ")[1];

        $($("th")[0]).attr("class","");
        $($("th")[12]).attr("class","");
    }
    else {
        column="Timestamp";
        order="sorting_desc";
    }


    var searchTerm = $("#quickSearch").val();
    if (searchTerm===""){getRepaginationData(); return 0;}
    // console.log("bypassed repagination", column, order);
    var isolatesTable_length = $("#isolatesTable_length").children().children().val();
    //var offset=isolatesTable_length * parseInt($(elem).attr('tabindex'))-isolatesTable_length;
    if (!offset){
        offset=0;
    }
    else {
        offset=offset;
    }
    // console.log("offset: " + offset);
    $.ajax({
        type: 'post',
        url: 'searchExtractsAjax',
        data: {length:isolatesTable_length, offset:offset, search:searchTerm, column: column, order:order},
        //data: {id:"1"},
        beforeSend: function(){
            //console.log(isolatesTable_length,offset,searchTerm,column,order);
        },
        success: function (response) {
            //console.log(response);
            var data=JSON.parse(response);
            var el;
            var next;
            var previous;
            if (!source) { //only redraw the pagination after a search, not after quickSearch gets called by pressing one of the pagination buttons
                //  console.log("source: " + source);
                pages=Math.ceil(data.count/data.size);
                count=data.count;
                size=data.size;
                //  console.log("pages: " + pages);
                var second = '<li class="paginate_button" aria-controls="isolatesTable" tabindex="2" onclick="select(this);return 0;"><a href="#">2</a></li>';
                var $el = $($.parseHTML(second));
                var elipsis1='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
                var $elipsis1=$($.parseHTML(elipsis1));

                if (pages>7){
                    // console.log(">7.2");
                    $($('*.pagination')).empty();
                    previous='<li class="paginate_button previous disabled" aria-controls="isolatesTable" tabindex="-4" id="isolatesTable_previous"><a href="#">Previous</a></li>';
                    next = '<li class="paginate_button next" aria-controls="isolatesTable" tabindex="-1" id="isolatesTable_next"> <a href="#">Next</a> </li>';
                    $($('*.pagination')).append(next);
                    el=$el.clone();
                    el.attr("tabindex", pages);
                    el.children().html(pages);
                    $($('*.pagination')).prepend(el);
                    var elipsis1_=$elipsis1.clone();
                    $($('*.pagination')).prepend(elipsis1_);

                    for (var i=5; i>0; i--){
                        el=$el.clone();
                        el.attr("tabindex", i);
                        el.children().html(i);
                        $($('*.pagination')).prepend(el);
                    }

                    $($('*.pagination')).prepend(previous);
                    $($('*.pagination').children().get(1)).addClass("active");
                    setButtons();

                    $('.next').removeClass("disabled");
                    $('.previous').addClass("disabled");
                }
                else {
                    //console.log("<7.2");
                    $($('*.pagination')).empty();

                    previous_='<li class="paginate_button previous disabled" aria-controls="isolatesTable" tabindex="-4" id="isolatesTable_previous"><a href="#">Previous</a></li>';
                    next = '<li class="paginate_button next" aria-controls="isolatesTable" tabindex="-1" id="isolatesTable_next"> <a href="#">Next</a> </li>';

                    $($('*.pagination')).append(next);
                    if (data.data.length>0){
                        for (var i=pages; i>0; i--){
                            el=$el.clone();
                            el.attr("tabindex", i);
                            el.children().html(i);
                            $($('*.pagination')).prepend(el);
                        }

                        $($('*.pagination')).prepend($($.parseHTML(previous_)).clone());
                        $($('*.pagination').children().get(1)).addClass("active");
                        setButtons();
                    }
                    else {
                        // console.log("no data");
                        $($('*.pagination')).prepend($($.parseHTML(previous_)).clone());
                        $('.next').addClass("disabled");

                    }
                }
            }


            tbodyIsolatesTemplate(data);


        },
        error: function() {
            console.error("error");
        }
    });

}




function getData(elem, tag) {
    // console.log("select() without quickSearch()");

    //console.log($(elem).html(), tag);
    ////// SET PAGINATION /////////////
    var isolatesTable_length;
    var offset;
    var column;
    var order;

    if(elem){ //if called from pagination, and not from the number of rows selection menu
        //console.log("select() tabindex: " + $(elem).attr("tabindex"));
        var elipsis0='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
        var elipsis1='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
        //var el='<li class="paginate_button" aria-controls="isolatesTable" tabindex="3" onclick="select(this);return 0;"><a href="#"></a></li>';
        var ind=$(elem).attr("tabindex");

        if (pages>1){
            if(ind==pages){
                // console.log("00");
                $(".next").addClass("disabled");
                $(".previous").removeClass("disabled");
            }
            else if(ind==1){
                // console.log("11");
                $(".previous").addClass("disabled");
                $(".next").removeClass("disabled");
            }
            else {
                // console.log("12");
                $(".previous").removeClass("disabled");
                $(".next").removeClass("disabled");
            }
        }


        if (ind>5 && ind < pages-5){
            $($('*.pagination').children().get(2)).replaceWith(elipsis0);
            $($('*.pagination').children().get(6)).replaceWith(elipsis1);
        }

        if (tag!==0){ //distinguish between calling from the pagination numbers, or from the pagination next/previous buttons
            if($(elem).attr("tabindex")==   pages && pages>7){

                $(".next").addClass("disabled");
                el=$($('*.pagination').children().get(4)).clone(true);
                $($('*.pagination').children().get(6)).replaceWith(el);
                $($('*.pagination').children().get(6)).attr("tabindex", pages-1);
                $($('*.pagination').children().get(6)).children().html(pages-1);
                $($('*.pagination').children().get(5)).attr("tabindex", pages-2);
                $($('*.pagination').children().get(5)).children().html(pages-2);
                $($('*.pagination').children().get(4)).attr("tabindex", pages-3);
                $($('*.pagination').children().get(4)).children().html(pages-3);
                $($('*.pagination').children().get(3)).attr("tabindex", pages-4);
                $($('*.pagination').children().get(3)).children().html(pages-4);
                var second = $('*.pagination').children().get(2);
                $(second).replaceWith(elipsis1);
                $(second).attr("tabindex", -2);

                if ($('.previous').attr("class").indexOf("disabled")>-1)$(".previous").removeClass("disabled");
            }

            else if($(elem).attr("tabindex")==1 && pages >7){
                console.log(2);
                $(".previous").addClass("disabled");
                var el=$($('*.pagination').children().get(3)).clone(true);
                $($('*.pagination').children().get(2)).replaceWith(el);
                $($('*.pagination').children().get(2)).attr("tabindex", "2");
                $($('*.pagination').children().get(2)).children().html("2");
                $($('*.pagination').children().get(3)).attr("tabindex", "3");
                $($('*.pagination').children().get(3)).children().html("3");
                $($('*.pagination').children().get(4)).attr("tabindex", "4");
                $($('*.pagination').children().get(4)).children().html("4");
                $($('*.pagination').children().get(5)).attr("tabindex", "5");
                $($('*.pagination').children().get(5)).children().html("5");

                var secondToLast = $('*.pagination').children().get(-3);
                $(secondToLast).replaceWith(elipsis1);
                if ($('.next').attr("class").indexOf("disabled")>-1)$(".next").removeClass("disabled");
            }

            if($(elem).attr("tabindex")>1 && $(elem).attr("tabindex")< pages) {
                if ($('.next').attr("class").indexOf("disabled")>-1)$(".next").removeClass("disabled");
                if ($('.previous').attr("class").indexOf("disabled")>-1)$(".previous").removeClass("disabled");
                if($(elem).attr("tabindex")>5 && ($(elem).attr("tabindex")!=pages)){
                    //console.log("9");
                    if($($('*.pagination').children().get(-2)).attr('tabindex')!=-3){
                        //var elipsis0 = $('#isolatesTable_ellipsis').clone(true);
                        $($('*.pagination').children().get(2)).replaceWith($($.parseHTML(elipsis0)).clone());
                    }
                }
            }
        }

        $('*.paginate_button').removeClass('active');
        $(elem).addClass('active');
        isolatesTable_length = $("#isolatesTable_length").children().children().val();
        offset=isolatesTable_length * parseInt($(elem).attr('tabindex'))-isolatesTable_length;

    }
    else { //if called from the #rows selection menu, instead of from the pagination menu
        isolatesTable_length = $("#isolatesTable_length").children().children().val();
        offset=0;
    }
    ////// END SET PAGINATION /////////////

    var searchTerm = $("#quickSearch").val();
    if (searchTerm!==""){
        // console.log("quicksearch");
        isolatesTable_length = $("#isolatesTable_length").children().children().val();
        offset=isolatesTable_length * parseInt($(elem).attr('tabindex'))-isolatesTable_length;
        //console.log("offset: " + offset)
        quickSearch(offset, 1);
    }

    else {

        if($(".sort").length > 0){
            elem=$(".sort");
            column=$(elem).html();
            switch(column) {
                case "Extract ID": column = "idExtract";
                    break;
                case "Sample ID ":  column = "Sample_Identifier";
                    break;
                case "Project ID":  column = "Project_Identifier";
                    break;
                case "Vol(µl)":  column = "Sample_Volume";
                    break;
                case "[ ](ng/µl)":  column = "Sample_Characterization(ng_ul)";
                    break;
                case "Strain Name":  column = "Strain_Name";
                    break;
                case "[ ] Type":  column = "Measument_Type";
                    break;
                case "Chimeric":  column = "Chimeric_Segments";
                    break;
                case "Cost Site":  column = "Cost_Site";
                    break;
                case "Date/Time":  column = "Timestamp";
                    break;
            }
            if (column.indexOf("Vol")>-1) column="Sample_Volume";
            if (column.indexOf("ng")>-1)  column="Sample_Concentration(ng_ul)";

            order=$(elem).attr("class").split(" ")[1];

            $($("th")[0]).attr("class","");
            $($("th")[12]).attr("class","");
        }
        else {
            column = "Timestamp";
            order  = "sorting_desc";
        }


        $.ajax({
            type: 'post',
            url: 'getExtractsAjax',
            data: {length:isolatesTable_length, offset:offset, column:column, order:order},
            //data: {id:"1"},
            beforeSend: function(){
                //console.log(isolatesTable_length, offset, column, order);
            },
            success: function (response) {
                //console.log(JSON.parse(response));
                tbodyIsolatesTemplate(JSON.parse(response));
            },
            error: function() {
                console.error("error");
            }
        });
    }
} //select()

function setStatusMulti(elem){
    self=elem;
    var value= self.value;
    var a = checkedRowsSet;
    for (var i=0; i<a.length; i++){
        var id="tr#"+a[i]
        var elem = $($(id).children()[2]);
        elem.attr("value", value);
        elem.html(value);

        var data = {
            value:elem.attr("value"),
            name:elem.attr("name"),
            id:elem.attr("id")
        }
        act(elem, data);

        //console.log("Val: " + value);


    }
    //console.log(checkedRowsSet);
    //$($("tr#340").children()[2]).attr("value")
}

function test(){
    return 1;
}