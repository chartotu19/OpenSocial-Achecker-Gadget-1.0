//:::::: to check whether the required fields is filled...If form completed, makes the Ajax call::::::::::
			function main_check()
			{var url_link=document.getElementById('url_link').value;
			 document.getElementById('result').style.display='none';
			 document.getElementById('loading').style.display='';
				
		//::::::::to read all the guidelines to be checked against::::::::::
		   if(typeof url_link !='undefined')
			{var first=0,against='';
			 for(i=1;i<10;i++)
				{if(document.getElementById('gid_'+i+'').checked)
				  {x=document.getElementById('gid_'+i+'').value;
					{if(first==0)
						{against=x;first=1;}
					 else
						  against=against+","+x;
					}
				  }
				}
			}
		//:::::::: retrieves the webservice_id of the user for signing the validation request :::::::::::    
			var webservice_id=prefs.getString("webserviceID");
			t=url_link.search("http://");
			if(t==-1)
				var url="http://achecker.ca/checkacc.php?uri=http://"+url_link+"&id="+webservice_id+"&output=rest&guide="+against+"&offset=0";
			else
				var url="http://achecker.ca/checkacc.php?uri="+url_link+"&id="+webservice_id+"&output=rest&guide="+against+"&offset=0";
			
			document.getElementsByName("uri_form")[0].value=url;
			document.getElementsByName("uri_form")[1].value=url;
			
		//::::::::::: creates params array and sets the content type to DOM ::::::::::::
		//::::::::::: using gadgets.io make the request to achecker server  ::::::::::::
			 
			var params={};  
			params[gadgets.io.RequestParameters.CONTENT_TYPE]=gadgets.io.ContentType.DOM;
			gadgets.io.makeRequest(url,report,params);
			}
			
		 //:::::::::: displays the final result in the gadget:::::::::::: 
			function report(obj){
			document.getElementById('result').style.display='';
			document.getElementById('loading').style.display='none';
		 
		//:::::::::: parsing data from the xml report :::::::::::::::::::
			var domdata = obj.data;
			x=domdata.getElementsByTagName('status')[0].childNodes[0].nodeValue;
			if(x=='FAIL')
				{document.getElementById('status').style.backgroundColor ='red';}
			if(x=='PASS')
				{document.getElementById('status').style.backgroundColor ="green";}
			if(x=='CONDITIONAL PASS')
				{document.getElementById('status').style.backgroundColor ="yellow";}
				
			var NumOfPotentialProblems=domdata.getElementsByTagName('NumOfPotentialProblems')[0].childNodes[0].nodeValue;
			var NumOfErrors=domdata.getElementsByTagName('NumOfErrors')[0].childNodes[0].nodeValue;
			var NumOfLikelyProblems=domdata.getElementsByTagName('NumOfLikelyProblems')[0].childNodes[0].nodeValue;

		//:::::::::: Displaying the summary in the result table::::::::::::::::::
			document.getElementById('status').innerHTML= x;
			document.getElementById('errors').innerHTML=NumOfErrors;
			document.getElementById('l_probs').innerHTML=NumOfLikelyProblems;
			document.getElementById('p_probs').innerHTML=NumOfPotentialProblems;
			
		//:::::::::: summary will be used to make the twitter updates::::::::::::::::::
			summary="total"+"^"+NumOfErrors+"^"+NumOfLikelyProblems+"^"+NumOfPotentialProblems;
			}

		   
		//:::::::::::: twitter update option::::::::::
			function twitter(){
				if(document.getElementById('errors').innerHTML!='')
					{window.open ("http://www.energisenow.elementfx.com/test/twitter/twitter_login.php?url="+url_link+"&total="+summary,"Twitter","menubar=1,resizable=1,width=600,height=350");
				}
				else
					alert('validate a url before tweeting!');
			}
		 
		//:::::::::::: Form validation functions::::::::::
			function validate(myobject){
				if(myobject.uri_form.value)
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