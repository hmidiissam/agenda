<? include("../include.inc");

$req_offre="select * from ext_remise_prix where remise_prix_id='".$_GET['offre']."'";
$res_offre=sql_query($req_offre);
$data_offre=sql_fetch_assoc($res_offre);


//calcul majoration

$mois = array("","Janvier","F�vrier","Mars","Avril","Mai","Juin","Juillet","Ao�t","Septembre","Octobre","Novembre","D�cembre");

/*
$sqlmajoration="SELECT *,SUBSTRING_INDEX( SUBSTRING_INDEX(periode, ' ', 1),'-',1) FROM `ext_majoration` where marchandise_id='".$data_offre["marchandise_id"]."' and UPPER (SUBSTRING_INDEX(periode, ' ', -1))='".strtoupper ($mois[date ("n",$data_offre['date_debut'])])."' and SUBSTRING_INDEX( SUBSTRING_INDEX(periode, ' ', 1),'-',1) <".date("d")." and SUBSTRING_INDEX( SUBSTRING_INDEX(periode, ' ', 1),'-',-1) >".date("d");
$resultatmajoration=sql_query($sqlmajoration);
$datateneurmajoration=sql_fetch_assoc($resultatmajoration);
*/
//fin majoration


//echo ($adresse_acheteur.'<br>'.$adresse_vendeur);
//calcul OS
//recuperer duree financement
$seq_duree="select * from ext_duree_financement where famille_id=(select ext_famille_id from ext_marchandise where ext_marchandise_id='".$data_offre["marchandise_id"]."') ";
$res_duree=sql_query($seq_duree);
$data_duree=sql_fetch_assoc($res_duree);
$nb_jours_financement=$data_duree['nb_jours'];

//recuperer taux financement
$seq_taux_financement="select * from ext_taux_financement";
$res_taux=sql_query($seq_taux_financement);

$date_debut_offre=  strtotime (str_replace("/","-",$data_offre["date_debut"]));

		while ($data_taux=sql_fetch_assoc($res_taux))
		{

				$date_debut_taux= strtotime (str_replace("/","-",$data_taux['debut'])) ;
				$date_fin_taux= strtotime (str_replace("/","-",$data_taux['fin'])) ;
				if ( ($date_debut_offre  > $date_debut_taux) and ($date_debut_offre  < $date_fin_taux))
						{
								$taux_financement=$data_taux['taux'];
						}

		}

// recuperer taux assurance
		$seq_taux_assurance="select * from ext_assurance_credit";
		$res_taux_assurance=sql_query($seq_taux_assurance);


				while ($data_taux_assurance=sql_fetch_assoc($res_taux_assurance))
				{

						$date_debut_taux= strtotime (str_replace("/","-",$data_taux_assurance['debut'])) ;
						$date_fin_taux= strtotime (str_replace("/","-",$data_taux_assurance['fin'])) ;
						if ( ($date_debut_offre  > $date_debut_taux) and ($date_debut_offre  < $date_fin_taux))
								{
										$taux_assurance=$data_taux_assurance['taux'];
								}

				}


// recuperer frais administratif
		$seq_frais_administratif="select * from ext_frais_administratif where famille=(select ext_famille_id from ext_marchandise where ext_marchandise_id='".$data_offre['marchandise_id']."')";
//echo $seq_frais_administratif;
		$res_frais_administratif=sql_query($seq_frais_administratif);


				while ($data_frais_administratif=sql_fetch_assoc($res_frais_administratif))
				{

						$date_debut_taux= strtotime (str_replace("/","-",$data_frais_administratif['debut'])) ;
						$date_fin_taux= strtotime (str_replace("/","-",$data_frais_administratif['fin'])) ;
						if ( ($date_debut_offre  > $date_debut_taux) and ($date_debut_offre  < $date_fin_taux))
								{
										$frais_administratif=$data_frais_administratif['taux'];
								}

				}

// recuperer taux  consititution depot garanti
		$seq_taux_consititution_depot_garanti="select * from ext_consititution_depot_garanti";
		$res_taux_consititution_depot_garanti=sql_query($seq_taux_consititution_depot_garanti);


						while ($data_taux_consititution_depot_garanti=sql_fetch_assoc($res_taux_consititution_depot_garanti))
						{

								$date_debut_taux= strtotime (str_replace("/","-",$data_taux_consititution_depot_garanti['debut'])) ;
								$date_fin_taux= strtotime (str_replace("/","-",$data_taux_consititution_depot_garanti['fin'])) ;
								if ( ($date_debut_offre  > $date_debut_taux) and ($date_debut_offre  < $date_fin_taux))
										{
												$taux_consititution_depot_garanti=$data_taux_consititution_depot_garanti['taux'];
										}

						}



$valeur_OS=((float)$data_offre["prix"]*(float)$taux_assurance)
						+
						(
							(float)$data_offre["prix"]
									*((float)$taux_financement/100)
									*((float)$nb_jours_financement/360)
						)
						+
						(float)$taux_consititution_depot_garanti
						+(float)$frais_administratif;


$valeur_OS= round($valeur_OS,2);
$note_OS=$data_offre["prix"]."*".$taux_assurance." +".$data_offre["prix"]."*".$taux_financement."/100.) *".$nb_jours_financement."/360)) + ".$taux_consititution_depot_garanti." +".$frais_administratif;
	$resultat= "{\"charge\":\"".$valeur_OS."\", \"note\":\"".$note_OS."\"}";

	echo $resultat;


//fin calcul OS


?>
