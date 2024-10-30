function countdown_clock(year, month, day, hour, minute, format, phpdate){
         //I chose a div as the container for the timer, but
         //it can be an input tag inside a form, or anything
         //who's displayed content can be changed through
         //client-side scripting.
         html_code = '<div id="countdown"></div>';
         
         document.write(html_code);
         
         countdown(year, month, day, hour, minute, format, phpdate);                
}
         
		 
function countdown(year, month, day, hour, minute, format, phpdate){
         Today = new Date();
		 //var msg1 = "  > Today (phpdate) is: " + phpdate;
		 //console.log(msg1);
		 
		 //var msg2 = "  >  Countdown Today: " + Today;
		 //console.log(msg2);
		 
         Todays_Year = Today.getFullYear() - 2000;
         Todays_Month = Today.getMonth();                  
         
         //Convert both today's date and the target date into miliseconds.                           
         Todays_Date = (new Date(Todays_Year, Todays_Month, Today.getDate(), 
                                 Today.getHours(), Today.getMinutes(), Today.getSeconds())).getTime();                                 
         Target_Date = (new Date(year, month - 1, day, hour, minute, 00)).getTime();                  
         
         //Find their difference, and convert that into seconds.                  
         Time_Left = Math.round((Target_Date - Todays_Date) / 1000);
         
         if(Time_Left < 0)
            Time_Left = 0;
         
         var innerHTML = '';
         
         switch(format)
               {
               case 0:
                    //The simplest way to display the time left.
                    innerHTML = Time_Left + ' seconds';
                    break;
               case 1:
                    //More datailed.
                    days = Math.floor(Time_Left / (60 * 60 * 24));
                    Time_Left %= (60 * 60 * 24);
                    hours = Math.floor(Time_Left / (60 * 60));
                    Time_Left %= (60 * 60);
                    minutes = Math.floor(Time_Left / 60);
                    Time_Left %= 60;
                    seconds = Time_Left;
                    
                    dps = 's'; hps = 's'; mps = 's'; sps = 's';
                    //ps is short for plural suffix.
                    if(days == 1) dps ='';
                    if(hours == 1) hps ='';
                    if(minutes == 1) mps ='';
                    if(seconds == 1) sps ='';
                    
                    innerHTML = '<span class="countdown_row countdown_show3">';
					innerHTML +=   '<span class="countdown_amount">';
					innerHTML +=     '<span class="countdown_section">' + days + '</span>';
                    innerHTML +=     '<span class="countdown_section">' + hours + '</span>';
                    innerHTML +=     '<span class="countdown_section">' + minutes + '</span>';
					innerHTML +=   '</span>';
					innerHTML += '</span>';
					
					innerHTML += '<span class="countdown_row countdown_show3">';
					innerHTML +=    '<span class="countdown_section">' + 'day' + dps + '</span>';
					innerHTML +=    '<span class="countdown_section">' + 'hour' + hps + '</span>';
					innerHTML +=    '<span class="countdown_section">' + 'minute' + mps + '</span>';
					innerHTML += '</span>';
					break;

				case 2:
                    //More datailed.
                    days = Math.floor(Time_Left / (60 * 60 * 24));
                    Time_Left %= (60 * 60 * 24);
                    hours = Math.floor(Time_Left / (60 * 60));
                    Time_Left %= (60 * 60);
                    minutes = Math.floor(Time_Left / 60);
                    Time_Left %= 60;
                    seconds = Time_Left;
                    
                    dps = 's'; hps = 's'; mps = 's'; sps = 's';
                    //ps is short for plural suffix.
                    if(days == 1) dps ='';
                    if(hours == 1) hps ='';
                    if(minutes == 1) mps ='';
                    if(seconds == 1) sps ='';
                    
                    innerHTML = '<span class="countdown_row countdown_show4">';
					innerHTML +=   '<span class="countdown_amount">';
					innerHTML +=     '<span class="countdown_section">' + days + '</span>';
                    innerHTML +=     '<span class="countdown_section">' + hours + '</span>';
                    innerHTML +=     '<span class="countdown_section">' + minutes + '</span>';
                    innerHTML +=     '<span class="countdown_section">' + seconds + '</span>';
					innerHTML +=   '</span>';
					innerHTML += '</span>';
					
					innerHTML += '<span class="countdown_row countdown_show4">';
					innerHTML +=    '<span class="countdown_section">' + 'day' + dps + '</span>';
					innerHTML +=    '<span class="countdown_section">' + 'hour' + dps + '</span>';
					innerHTML +=    '<span class="countdown_section">' + 'minute' + dps + '</span>';
					innerHTML +=    '<span class="countdown_section">' + 'second' + dps + '</span>';
					innerHTML += '</span>';
                    break;	
               default: 
                    innerHTML = Time_Left + ' seconds';
               }                   
                    
            document.getElementById('countdown').innerHTML = innerHTML;     
               
         //Recursive call, keeps the clock ticking.
		 // here we need to add 1 second to phpdate ... 
         setTimeout('countdown(' + year + ',' + month + ',' + day + ',' + hour + ',' + minute + ',' + format + ',"' + phpdate + '");', 1000);
    }