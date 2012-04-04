//:::::: to check whether the required fields is filled...If form completed, makes the Ajax call::::::::::
  
function main_check(){ 
    var html=document.getElementById('url_link').value;
//::::: removes the previous result :::::::::	 
	document.getElementById('result').style.display='none';
//::::: displays the loading gif :::::::::
    document.getElementById('loading').style.display='';
//::::::::to read all the guidelines to be checked against::::::::::
	if(typeof html !='undefined')
		{
		var first=0,against='';
		for(i=1;i<10;i++)
			{
			if(document.getElementById('gid_'+i+'').checked)
				{
				x=document.getElementById('gid_'+i+'').value;
				if(first==0)
					against=x;first=1;
				else
					against=against+","+x;
				}
			}
		}
//:::::: prefs.getstring() gets the already saved webservice id of the user :::::::
	var webservice_id=prefs.getString("webserviceID");
    t=html.search("http://");
    if(t==-1)
		var url="http://achecker.ca/checkacc.php?uri=http://"+html+"&id="+webservice_id+"&output=rest&guide="+against+"&offset=0";
    else
		var url="http://achecker.ca/checkacc.php?uri="+html+"&id="+webservice_id+"&output=rest&guide="+against+"&offset=0";
     
//:::::::::complete url for xml and html download options:::::::::::::::    
    document.getElementsByName("uri_form")[0].value=url;
    document.getElementsByName("uri_form")[1].value=url;

//:::::::: website being checked taken for pdf, earl and csv download options  :::::::::::::::::::::::::::
//:::::::: guidelines against which validation is done - for pdf, earl and csv download options:::::::::::
	for(i=0;i<3;i++)
		{
		document.getElementsByName("url")[i].value=document.getElementById('url_link').value;
		document.getElementsByName("guide")[i].value=against;
		} 
    
//::::::::::: Making the request to achecker webserver to get the xml report :::::::::::::::
    var params={};  
    params[gadgets.io.RequestParameters.CONTENT_TYPE]=gadgets.io.ContentType.DOM;
    gadgets.io.makeRequest(url,report,params);
	}


//:::::::::: displays the final result in the gadget:::::::::::: 
function report(obj){
    document.getElementById('result').style.display='';
    document.getElementById('loading').style.display='none';
    var domdata = obj.data;
    x=domdata.getElementsByTagName('status')[0].childNodes[0].nodeValue;
    if(x=='FAIL')
		{document.getElementById('status').style.backgroundColor ='red';}
    if(x=='PASS')
    	{document.getElementById('status').style.backgroundColor ="green";}
    if(x=='CONDITIONAL PASS')
    	{document.getElementById('status').style.backgroundColor ="yellow";}
    var a=0,b=0,c=0;  
    a=domdata.getElementsByTagName('NumOfErrors')[0].childNodes[0].nodeValue;
    b=domdata.getElementsByTagName('NumOfLikelyProblems')[0].childNodes[0].nodeValue;
    c=domdata.getElementsByTagName('NumOfPotentialProblems')[0].childNodes[0].nodeValue;

//::::::::: displaying the summary:::::::::::::
	document.getElementById('status').innerHTML= x;
    document.getElementById('errors').innerHTML=a;
    document.getElementById('l_probs').innerHTML=b;
    document.getElementById('p_probs').innerHTML=c;  
    
	var total=parseInt(a)+parseInt(b)+parseInt(c);
    var text=total+"^"+a+"^"+b+"^"+c;
//:::::::::total errors found - for pdf, earl and csv download options:::::::::::::::      
    for(i=0;i<5;i++)
		{
		document.getElementsByName("total")[i].value=text;
		}
	var i=0,j=0,k=0,l=0,m=0;
	var final=" ";
	var error_details=" ";
	var l_prob_details=' ';
	var p_prob_details=' ';
	var field=" ";
	var detail=new Array();

//:::::::::::::creates the required number of <tr> tags and the input fields:::::::::::::::   
	for(i=0;i<a;i++)
		{
		error_details=error_details+"<tr id='error"+i+"'></tr>";
		field=field+"<input type='hidden' name='post_error"+i+"' id='post_error"+i+"'value=''/>";
		}
       
	for(i=0;i<b;i++)
		{
		l_prob_details=l_prob_details+"<tr id='l_prob"+i+"'></tr>";
		field=field+"<input type='hidden' name='post_l_prob"+i+"' id='post_l_prob"+i+"'value=''/>";
		}
       
	for(i=0;i<c;i++)
		{
		p_prob_details=p_prob_details+"<tr id='p_prob"+i+"'></tr>";
		field=field+"<input type='hidden' name='post_p_prob"+i+"' id='post_p_prob"+i+"'value=''/>";
		}
	
	document.getElementById('type0').style.background='orange';
	document.getElementById('type1').style.background='orange';
	document.getElementById('type2').style.background='orange';
	document.getElementById('type3').style.background='orange';

//::::::::::: making the error(potential,likely,errors) divs visible
	document.getElementById('error_details').style.display='';
    document.getElementById('l_prob_details').style.display='';  
    document.getElementById('p_prob_details').style.display=''; 
    
//::::::::::: assigning the empty tables to the divs
	document.getElementById('error_details').innerHTML=error_details;
    document.getElementById('l_prob_details').innerHTML=l_prob_details; 
    document.getElementById('p_prob_details').innerHTML=p_prob_details; 	  
    document.getElementById('postfield1').innerHTML=field;
    document.getElementById('postfield2').innerHTML=field;
    document.getElementById('postfield3').innerHTML=field;
      
	i=0;j=0;k=1;var temp='';var check='';var flag0=0,flag1=0,flag2=0;
      
//:::::::::::parses the error and displays it in a table form:::::::::::::::
    while(i<total){
		temp=domdata.getElementsByTagName('errorSourceCode')[i].childNodes[0].nodeValue;
		for(j=0;j<8;j++)
			temp=temp.replace("<", "&lt;");
			check=domdata.getElementsByTagName('resultType')[i].childNodes[0].nodeValue; 
			data="<td class='qwe'>"+domdata.getElementsByTagName('resultType')[i].childNodes[0].nodeValue+"</td><td class='qwe'>"+domdata.getElementsByTagName('lineNum')[i].childNodes[0].nodeValue+"</td><td class='qwe'>"+domdata.getElementsByTagName('columnNum')[i].childNodes[0].nodeValue+"</td><td class='qwe'>"+domdata.getElementsByTagName('errorMsg')[i].childNodes[0].nodeValue+"</td><td class='qwe'><code>"+temp+"</code></td>";
			val=domdata.getElementsByTagName('resultType')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('columnNum')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('lineNum')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('errorMsg')[i].childNodes[0].nodeValue;
			val1=domdata.getElementsByTagName('resultType')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('columnNum')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('lineNum')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('errorMsg')[i].childNodes[0].nodeValue+"^"+domdata.getElementsByTagName('errorSourceCode')[i].childNodes[0].nodeValue;
			if(check=='error' || check=='Error')
				{
				document.getElementById('error'+flag0).innerHTML=data;
				document.getElementsByName('post_error'+flag0)[0].value=val;
				document.getElementsByName('post_error'+flag0)[1].value=val;
				document.getElementsByName('post_error'+flag0)[2].value=val1;
				flag0++;
				}
			else if(check=='Likely Problem')
				{
				document.getElementById('l_prob'+flag1).innerHTML=data;
				document.getElementsByName('post_l_prob'+flag1)[0].value=val;
				document.getElementsByName('post_l_prob'+flag1)[1].value=val;
				document.getElementsByName('post_l_prob'+flag1)[2].value=val1;
				flag1++;
				}
			else
				{
				document.getElementById('p_prob'+flag2).innerHTML=data;
				document.getElementsByName('post_p_prob'+flag2)[0].value=val;
				document.getElementsByName('post_p_prob'+flag2)[1].value=val;
				document.getElementsByName('post_p_prob'+flag2)[2].value=val1;
				flag2++;
				}
			i++;
		}	
	}

//:::::::::::: Error sorting buttons:::::::::::::
function display(type,id){
	var arr=['error_details','l_prob_details','p_prob_details']; 
	var ids=['type0','type1','type2','type3'];
	if(id!='type3')        
		{
		arr.splice(arr.indexOf(type), 1);
        for(i=0;i<2;i++)
			document.getElementById(arr[i]).style.display='none';
		ids.splice(ids.indexOf(id), 1);
		for(i=0;i<3;i++)
			document.getElementById(ids[i]).style.background='#EEE';
        document.getElementById(type).style.display='';
        document.getElementById(id).style.background='orange';
        }
    else{
        for(i=0;i<3;i++)
			document.getElementById(arr[i]).style.display='';
        for(i=0;i<4;i++)
            document.getElementById(ids[i]).style.background='orange';
        }
	}

//::::::::::::: form validation function:::::::::::::::	
function validate(myobject){
	if(myobject.total.value)
		return true;
	else
		return false;
	}

function check(formObject){
	if(validate(formObject))
		formObject.submit();
	else
		alert('Validate a url before downloading!!');
	}

 //:::::::::::: twitter update option::::::::::
function twitter(){
	if(document.getElementById('errors').innerHTML!='')
		{
        url=document.getElementsByName('url')[0].value;
        total=document.getElementsByName('total')[0].value;
        window.open ("http://www.energisenow.elementfx.com/test/twitter/twitter_login.php?url="+url+"&total="+total,"Twitter","menubar=1,resizable=1,width=600,height=350");
        }
	else
		alert('validate a url before tweeting!');
	}