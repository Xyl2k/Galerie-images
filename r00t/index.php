<?php
	

class omg {
	//-----------------------------------------------
	// Gallerie d'images by Moi même
	//-----------------------------------------------
	#############################----START OF CONFIG----#############################
	var $afficher_nom = 0; //1 pour afficher le nom de l'image sinon, 0
	var $enlever_extention = 1; //1 pour enlever l'extention sinon dans l'affichage du nom de l'image. 0 pour afficher l'extention (ne fonctionne que si $afficher_nom est sur 1)
	var $remplacer_ = 1; //1 pour remplacer les _ par des espaces dans les nom de fichier sinon, 0 (ne fonctionne que si $afficher_nom est sur 1)
	var $titre = "Galerie root - Xylitol"; // titre de la page
	var $couleur_texte = "#000000"; //Code couleur en hexa (#FFFFFF pour blanc, #000000 pour noir, ect...)
	var $largeur_miniatures = 200; //largeur maxi de la miniature
	var $hauteur_miniatures = 200; //hauteur maxi de la miniature
	var $imagesaafficherparligne = 5; //tout est dit dans le nom de la variable ^^
	var $ext_autorise = array('png', 'jpg', 'gif', 'jpeg'); // Ne pas toucher...
	##############################----ENF OF CONFIG----#############################
	
function afficherHeader() {
	echo "<html>\n<head>\n<title>".$this->titre."</title>";
?>

<script type="text/javascript" src="../lbox/highslide/highslide-with-gallery.js"></script>
<link rel="stylesheet" type="text/css" href="../lbox/highslide/highslide.css" />

<!--
	2) Optionally override the settings defined at the top
	of the highslide.js file. The parameter hs.graphicsDir is important!
-->

<script type="text/javascript">
	hs.graphicsDir = '../lbox/highslide/graphics/';
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.outlineType = 'rounded-white';
	hs.fadeInOut = true;
	hs.numberPosition = 'caption';
	hs.dimmingOpacity = 0.75;

	// Add the controlbar
	if (hs.addSlideshow) hs.addSlideshow({
		//slideshowGroup: 'group1',
		interval: 5000,
		repeat: false,
		useControls: true,
		fixedControls: 'fit',
		overlayOptions: {
			opacity: .75,
			position: 'bottom center',
			hideOnMouseOut: true
		}
	});

</script>



<body bgcolor="#000000">
<?php echo "<style type=\"text/css\">a:link, a:visited, a:active {text-decoration: none; color: #FFFFFF}</style>\n</head>\n<body text=\"".$this->couleur_texte."\">\n<table width=\"100%\" border=\"0\">\n";
}
function afficherFooter() {
	echo "</table></div>\n</body>\n</html>";
}

function listerDossier() {
	$main = opendir('.');
	while(($n = readdir($main)) !== false) {
		$tempext = strtolower(substr(strrchr($n, '.'),1));
		if(is_file($n) && in_array($tempext, $this->ext_autorise)) {
			$images[] = $n;
		}
	}
	if(empty($images)) { die($this->afficherHeader()."<tr><td align=\"center\">Vous n'avez pas d'images dans le dossier !!!</td></tr>\n").$this->afficherFooter(); }
	sort($images);
	$this->afficherHtml($images);
}
function afficherHtml($images) {
	echo $this->afficherHeader();
	$x = 0;
	while(@($images[$x] != '')) {
		echo "<tr>\n";
		for($i=1;$i<=$this->imagesaafficherparligne;$i++) {
			if(!empty($images[$x])) {
				if($this->afficher_nom == 1) {
					$nom = $images[$x];
					if($this->enlever_extention == 1) { $nom = ereg_replace(substr(strrchr($images[$x], '.'),0), '', $images[$x]); }
					if($this->remplacer_ == 1) { $nom = ereg_replace('_', ' ', $nom); }
					$nom = "<br /><span style='color:#000000'>".$nom."</span><br /><br />";
				}
				echo '<td width="'.intval(100 / $this->imagesaafficherparligne).'%" align="center"><a href="'.$images[$x].'" alt="" border="0" width="200" height="200" class="highslide" onclick="return hs.expand(this)"><img src="'.$images[$x].'" width="200" height="200" alt="Highslide JS" title="Click to enlarge" /></a><td>'."\n";
				$x++;
			}
		}
		echo "</tr>\n";
	}
	echo $this->afficherFooter();
}
function genererMiniatures($img) {
	if(!function_exists('gd_info')) { die('La librairie GD n\'est pas activée sur votre serveur !!'); }
	$tempext = strtolower(substr(strrchr($img, '.'),1));
	if(!in_array($tempext, $this->ext_autorise));
	if($tempext == 'jpg' || $tempext == 'jpeg') {
		$source = @imagecreatefromjpeg($img);
	} elseif($tempext == 'gif') {
		$source = @imagecreatefromgif($img);
	} elseif($tempext == 'png') {
		$source = @imagecreatefrompng($img);
	} else {  die('Error !'); }
	$infos = @getimagesize($img);
	$largeur_original = $infos[0];
	$hauteur_original = $infos[1];
	while($infos[0] > $this->largeur_miniatures || $infos[1] > $this->hauteur_miniatures) {
		$infos[0] = intval($infos[0] / 1.5);
		$infos[1] = intval($infos[1] / 1.5);
	}
	$largeur_new = $infos[0];
	$hauteur_new = $infos[1];
	$img = @imagecreatetruecolor($largeur_new,$hauteur_new);
	@imagecopyresized($img,$source,0,0,0,0,$largeur_new,$hauteur_new,$largeur_original,$hauteur_original);
	@header('Content-type: image/jpeg');
	@imagejpeg($img, '', 100);
}
}
$class = new omg();
if(!empty($_GET['img'])) {
	$img = get_magic_quotes_gpc() ? stripslashes($_GET['img']) : $_GET['img'];
	$class->genererMiniatures($img);
} else {
	$class->listerDossier();
}
?>