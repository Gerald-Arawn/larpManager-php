<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-06-30 20:35:19.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BasePersonnage;

/**
 * LarpManager\Entities\Personnage
 *
 * @Entity(repositoryClass="LarpManager\Repository\PersonnageRepository")
 */
class Personnage extends BasePersonnage
{
	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setXp(0);
	}
	
	public function __toString()
	{
		return $this->getPublicName();
	}
	
	/**
	 * Fourni le surnom si celui-ci a été précisé
	 * sinon fourni le nom
	 */
	public function getPublicName()
	{
		if ( $this->getSurnom() ) return $this->getSurnom();
		return $this->getNom();
	}
	
	/**
	 * Fourni l'identité complete d'un personnage
	 */
	public function getIdentity()
	{
		$groupe = $this->getGroupe();
		$participant = $this->getParticipant();
		
		$identity = $this->getPublicName().' (';
		if ( $groupe ) $identity .= $groupe->getNom();
		if ( $participant ) $identity .= " - ". $participant->getUser()->getUsername();
		$identity .= ')';
		return $identity;
	}
	
	/**
	 * Indique si le personnage est un Fanatique
	 */
	public function isFanatique()
	{
		$personnagesReligions = $this->getPersonnagesReligions();
		foreach ( $personnagesReligions as $personnageReligion )
		{
			if ( $personnageReligion->getReligionLevel()->getIndex() == 3 )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Indique si le personnage est un Fervent
	 */
	public function isFervent()
	{
		$personnagesReligions = $this->getPersonnagesReligions();
		foreach ( $personnagesReligions as $personnageReligion )
		{
			if ( $personnageReligion->getReligionLevel()->getIndex() == 2 )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Fourni la liste des groupes secondaires pour lesquel ce personnage est chef
	 */
	public function getSecondaryGroupsAsChief()
	{
		return $this->getSecondaryGroups();
	}
	
	/**
	 * Fourni la description du membre correspondant au groupe passé en paramètre
	 * @param SecondaryGroup $groupe
	 */
	public function getMembre(SecondaryGroup $groupe)
	{
		foreach ($this->getMembres() as $membre)
		{
			if ( $membre->getSecondaryGroup() == $groupe)
				return $membre;
		}
		return false;
	}
	
	/**
	 * Ajoute des points d'experience à un personnage
	 *  
	 * @param integer $xp
	 */
	public function addXp($xp)
	{
		$this->setXp($this->getXp() + $xp);
		return $this;
	}
	
	/**
	 * Retire des points d'expérience à un personnage
	 * @param integer $xp
	 */
	public function removeXp($xp)
	{
		$this->setXp($this->getXp() - $xp);
		return $this;
	}
	
	/**
	 * Recupère le nom de classe genrifié du personnage 
	 * @todo : Evoluer vers un modèle de données ou les libélés de ressource sont variable en fonction du genre
	 */
	public function getClasseName()
	{
		$lGenreMasculin = true;
		if($this->getGenre() != null)
		{
			$lGenreMasculin = $this->getGenre()->getLabel() == "Masculin";
		}
		
		if($this->getClasse() == null) 
		{
			return '';
		}
		else if($lGenreMasculin)
		{
			return $this->getClasse()->getLabelMasculin();
		}
		else 
		{
			return $this->getClasse()->getLabelFeminin();
		}
		
	}
	
	/**
	 * Retire un personnage d'un groupe
	 * 
	 * @param Groupe $groupe
	 */
	public function removeGroupe(Groupe $groupe)
	{
		$groupe->removePersonnage($this);
		$this->setGroupe(null);
	}
				
}