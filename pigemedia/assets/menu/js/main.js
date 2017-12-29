jQuery(document).ready(function($){
	var overlayNav = $('.cd-overlay-nav'),
		overlayContent = $('.cd-overlay-content'),
		navigation = $('.cd-primary-nav'),
		toggleNav = $('.cd-nav-trigger');

		/*toggleNav.addClass('close-nav');
			//alert("lasa ary hoe");
			overlayNav.children('span').velocity({
				translateZ: 0,
				scaleX: 1,
				scaleY: 1,
			}, 500, 'easeInCubic', function(){
				navigation.addClass('fade-in');
			});*/
		 var user="";
		 var droit="";

		 //********** filtre etat bc
	  function filtrer_bc(){
            var tot_lig_hot = parseInt($("#nb_devis").val());
            var ville_a_rechercher = $("#etat_bc").val();
			if (ville_a_rechercher > "") {
                for (var i = 0; i <= tot_lig_hot; i++) {
                    if ($("#ligne_bc_" + i).attr("etat") == ville_a_rechercher) {
                        $("#ligne_bc_" + i).show();
                    } else {
                        $("#ligne_bc_" + i).hide();
                    }
                }
            }else{
				for (var i = 0; i <= tot_lig_hot; i++) {
                        $("#ligne_bc_" + i).show();
                }
			}
	 }
// fin
	function Deconnecter(){
		//alert("dec");
		 $.post(
            '/syndesign3/modeles/Deconnecter.php', // Un script PHP que l'on va créer juste après
            {
                login : $("#loginEmail").val(), // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                password : $("#loginPass").val()
            },

            function(data){

                if(data == 0){
                   //Verifier_connection(); 
                }
                else{
                   
                }
        
            },

            'text'
         );



	}
	function Verifier_connection(){
	
         	var xhr = getXhr();
            // On défini ce qu'on va faire quand on aura la réponse
            xhr.onreadystatechange = function () 
                {
                // On ne fait quelque chose que si on a tout reçu et que le serveur est ok
                if (xhr.readyState == 4 && xhr.status == 200) {
                    retour_php = xhr.responseText;
                    //alert(retour_php);
                    if(retour_php==1)
                    {
                        //alert("ok");
                       // window.location.replace("/syndesign3/assets/authentification/");
                        /*if (location.protocol != 'https:')
							{
								alert("no");
 							location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
							}*/
                    }
                    else
                    {
                        // window.location.replace("");
                      /*  user=retour_php.split('|')[0];
                        droit=retour_php.split('|')[1];
                        utilisateurs=retour_php.split('|')[2];
                        //alert(user+" vide "+retour_php+" vide "+droit);*/
                        try{
                        /*document.getElementById("user").innerHTML=user;
                        document.getElementById("utilisateurs_attendus").innerHTML=utilisateurs;*/
                    	}catch(e){}
                    }
                }
            }
    
            // Ici on va voir comment faire du post
            xhr.open("POST", "/syndesign3/modeles/Verification_connection.php", true);
            // ne pas oublier ça pour le post
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // ne pas oublier de poster les arguments
            // ici, l'id de l'auteur
    
            xhr.send("idAuteur=" + "ok");
        
        
     
}
	//inizialize navigation and content layers
	layerInit();
	Verifier_connection();

	$(window).on('resize', function(){
		window.requestAnimationFrame(layerInit);
	});

	$('.cd-nav-trigger3').on('click', function(){
		Deconnecter();
	});
	//open/close the menu and cover layers
	toggleNav.on('click', function(){

		if(!toggleNav.hasClass('close-nav')) {
			//it means navigation is not visible yet - open it and animate navigation layer
			toggleNav.addClass('close-nav');
			//alert("lasa ary hoe");
			overlayNav.children('span').velocity({
				translateZ: 0,
				scaleX: 1,
				scaleY: 1,
			}, 500, 'easeInCubic', function(){
				navigation.addClass('fade-in');
			});
		} else {
			//navigation is open - close it and remove navigation layer
			toggleNav.removeClass('close-nav');
			
			overlayContent.children('span').velocity({
				translateZ: 0,
				scaleX: 1,
				scaleY: 1,
			}, 500, 'easeInCubic', function(){
				navigation.removeClass('fade-in');
				
				overlayNav.children('span').velocity({
					translateZ: 0,
					scaleX: 0,
					scaleY: 0,
				}, 0);
				
				overlayContent.addClass('is-hidden').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
					overlayContent.children('span').velocity({
						translateZ: 0,
						scaleX: 0,
						scaleY: 0,
					}, 0, function(){overlayContent.removeClass('is-hidden')});
				});
				if($('html').hasClass('no-csstransitions')) {
					overlayContent.children('span').velocity({
						translateZ: 0,
						scaleX: 0,
						scaleY: 0,
					}, 0, function(){overlayContent.removeClass('is-hidden')});
				}
			});
		}
	});

	function layerInit(){
		var diameterValue = (Math.sqrt( Math.pow($(window).height(), 2) + Math.pow($(window).width(), 2))*2);
		overlayNav.children('span').velocity({
			scaleX: 0,
			scaleY: 0,
			translateZ: 0,
		}, 50).velocity({
			height : diameterValue+'px',
			width : diameterValue+'px',
			top : -(diameterValue/2)+'px',
			left : -(diameterValue/2)+'px',
		}, 0);

		overlayContent.children('span').velocity({
			scaleX: 0,
			scaleY: 0,
			translateZ: 0,
		}, 50).velocity({
			height : diameterValue+'px',
			width : diameterValue+'px',
			top : -(diameterValue/2)+'px',
			left : -(diameterValue/2)+'px',
		}, 0);
	}
});