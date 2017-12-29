


var user="";
var droit="";


$(function(){

	/*Fonction de base AJAX pour interragir avec un fichier php*/
 	
	
	// Checking for CSS 3D transformation support
	$.support.css3d = supportsCSS3D();
	
	var formContainer = $('#formContainer');
	
	// Listening for clicks on the ribbon links
	$('.flipLink').click(function(e){
		
		// Flipping the forms
		formContainer.toggleClass('flipped');
		
		// If there is no CSS3 3D support, simply
		// hide the login form (exposing the recover one)
		if(!$.support.css3d){
			$('#login').toggle();
		}
		e.preventDefault();
	});
	
	formContainer.find('form').submit(function(e){
		// Preventing form submissions. If you implement
		// a backend, you might want to remove this code
		//e.preventDefault();
		  $.post(
            '../../modeles/Verifier_utilisateur.php', // Un script PHP que l'on va créer juste après
            {
                login : $("#loginEmail").val(), // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                password : $("#loginPass").val()
            },

            function(data){

                if(data == 0){
                     // Le membre est connecté. Ajoutons lui un message dans la page HTML.
                     alert("echec de connection");
                    // $("#resultat").html("<p>Vous avez été connecté avec succès !</p>");
                }
                else{
                     // Le membre n'a pas été connecté. (data vaut ici "failed")
                     //alert(data);
                     user=data.split(':')[1];
                     droit=data.split(':')[0];
                     //alert(user+" "+droit);
                     url='./../../';
                     nurl=url+user;
                      window.location.replace(url);
                     //$("#resultat").html("<p>Erreur lors de la connexion...</p>");
                }
        
            },

            'text'
         );
	});
	
	
	// A helper function that checks for the 
	// support of the 3D CSS3 transformations.
	function supportsCSS3D() {
		var props = [
			'perspectiveProperty', 'WebkitPerspective', 'MozPerspective'
		], testDom = document.createElement('a');
		  
		for(var i=0; i<props.length; i++){
			if(props[i] in testDom.style){
				return true;
			}
		}
		
		return false;
	}
});
