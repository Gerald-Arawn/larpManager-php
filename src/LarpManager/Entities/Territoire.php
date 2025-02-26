<?php

/**
 * LarpManager - A Live Action Role Playing Manager
 * Copyright (C) 2016 Kevin Polez
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-08-19 10:58:55.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BaseTerritoire;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LarpManager\Entities\Territoire
 * 
 * Je définie les relations ManyToMany içi au lieu de le faire dans Mysql Workbench
 * car l'exporteur ne sait pas gérer correctement plusieurs relations ManyToMany entre 
 * les mêmes entities (c'est dommage ...)
 *
 * @Entity(repositoryClass="LarpManager\Repository\TerritoireRepository")
 */
class Territoire extends BaseTerritoire implements \JsonSerializable
{
	/**
	 * @ManyToMany(targetEntity="Ressource", inversedBy="importateurs" )
	 * @JoinTable(name="territoire_importation",
	 *     joinColumns={@JoinColumn(name="territoire_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@JoinColumn(name="ressource_id", referencedColumnName="id")}
	 * )
	 */
	protected $importations;
	
	/**
	 * @ManyToMany(targetEntity="Ressource", inversedBy="exportateurs")
	 * @JoinTable(name="territoire_exportation",
	 *     joinColumns={@JoinColumn(name="territoire_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@JoinColumn(name="ressource_id", referencedColumnName="id")}
	 * )
	 */
	protected $exportations;
	
	/**
	 * @ManyToMany(targetEntity="Langue", inversedBy="territoireSecondaires")
	 * @JoinTable(name="territoire_langue",
	 *     joinColumns={@JoinColumn(name="territoire_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@JoinColumn(name="langue_id", referencedColumnName="id")}
	 * )
	 */
	protected $langues;
	
	/**
	 * @ManyToMany(targetEntity="Religion", inversedBy="territoireSecondaires")
	 * @JoinTable(name="territoire_religion",
	 *     joinColumns={@JoinColumn(name="territoire_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@JoinColumn(name="religion_id", referencedColumnName="id")}
	 * )
	 */
	protected $religions;
	
	/**
	 * Constructeur
	 */
	public function __construct()
	{
		$this->importations = new ArrayCollection();
		$this->exportations = new ArrayCollection();
		$this->langues = new ArrayCollection();
		$this->religions = new ArrayCollection();
		$this->setOrdreSocial(3);
		parent::__construct();
	}
	
	/**
	 * Serializer
	 */
	public function jsonSerialize() {
		return array(
				'id' => $this->getId(),
				'nom' => $this->getNom(),
				'description' => $this->getDescription(),
				'capitale' => $this->getCapitale(),
				'politique' => $this->getPolitique(),
				'dirigeant' => $this->getDirigeant(),
				'population' => $this->getPopulation(),
				'symbole' => $this->getSymbole(),
				'tech_level' => $this->getTechLevel(),
				'type_racial' => $this->getTypeRacial(),
				'inspiration' => $this->getInspiration(),
				'armes_predilection' => $this->getArmesPredilection(),
				'vetements' => $this->getVetements(),
				'noms_masculin' => $this->getNomsMasculin(),
				'noms_feminin' => $this->getNomsFeminin(),
				'frontieres' => $this->getFrontieres(),
				'religion_id' => ( $this->getReligion() ) ? $this->getReligion()->getId() : '',
				/*'chronologies'
				'groupes'
				'langue_principale'
				'religion_principale'
				'langues'
				'religions'
				'importations'
				'exporations'*/
				
		);
	}
	
	/**
	 * Unserializer
	 * 
	 * @param unknown $payload
	 */
	public function jsonUnserialize($payload) {
		$this->setNom($payload->nom);
		$this->setDescription($payload->description);
		$this->setCapitale($payload->capitale);
		$this->setPolitique($payload->politique);
		$this->setDirigeant($payload->dirigeant);
		$this->setPopulation($payload->population);
		$this->setSymbole($payload->symbole);
		$this->setTechLevel($payload->tech_level);
		$this->setTypeRacial($payload->type_racial);
		$this->setInspiration($payload->inspiration);
		$this->setArmesPredilection($payload->armes_predilection);
		$this->setVetements($payload->vetements);
		$this->setNomsMasculin($payload->noms_masculin);
		$this->setNomsFeminin($payload->noms_feminin);
		$this->setFrontieres($payload->frontieres);
	}
	
	/**
	 * Affichage
	 */
	public function __toString()
	{
		return $this->getNom();
	}
	
	/**
	 * Determine si un territoire dispose d'une construction
	 * 
	 * @param unknown $label
	 */
	public function hasConstruction($label)
	{
		foreach ( $this->getConstructions() as $construction)
		{
			if ( $construction->getLabel() == $label ) return true;
		}
		return false;
	}
	
	/**
	 * Fourni la richesse d'un territoire, en fonction de son statut (1/2 si désordre, 1 pièce si désolation), et des constructions
	 */
	public function getRichesse()
	{
		$tresor = parent::getTresor();
		if ( ! $tresor) $tresor = 0;
		
		// gestion de l'état du territoire
		switch ($this->getStatut() )
		{
			case 'Normal': return $tresor;
			case 'Désordre': return round($tresor/ 2);
			case 'Désolation': return 1;
			default : return $tresor;
		}
	}
	
	/**
	 * Fourni la culture d'un territoire ou à défaut la culture du territoire parent.
	 */
	public function getCulture()
	{
		if ( $this->culture )
		{
			return parent::getCulture();
		}
		else if ( $this->getTerritoire())
		{
			return $this->getTerritoire()->getCulture();
		}
		return null;		
	}
	
	/**
	 * Fourni le nombre de personnages nobles rattachés à ce territoire
	 */
	public function getNbrNoble()
	{
		$nobles= [];
		foreach ( $this->getGroupesFull() as $groupe)
		{
			foreach ( $groupe->getPersonnages() as $personnage )
			{
				if ( $personnage->hasCompetence('Noblesse')){
					$nobles[] = $personnage->getId();
				}
			}
		}
		foreach ($this->getTerritoires() as $territoire)
		{
			$nobles = array_unique(array_merge($nobles, $territoire->getNbrNoble()));
			/*
			echo "<pre>";
			echo $territoire->getNom()." : ".count($territoire->getNbrNoble())."/".count($nobles);
			echo "</pre>";
			echo "<hr />";
			*/
		}
		return array_unique($nobles);
	}
	
	/**
	 * Fourni la defense d'un territoire
	 */
	public function getDefense()
	{
		$defense = 0;
		if ( $this->getResistance() )
		{
			$defense += $this->getResistance();
		}
		
		foreach ( $this->getConstructions() as $construction)
		{
			$defense += $construction->getDefense();
		}
		return $defense;
	}
	
	/**
	 * Fourni le territoire racine
	 */
	public function getRoot()
	{
		if ( $this->getTerritoire() )
		{
			return $this->getTerritoire()->getRoot();	
		}
		else
		{
			return $this;
		}		
	}

	/**
	 * Fourni l'arbre des territoires'
	 */
	public function getTree()
	{
		if ( $this->getTerritoire() )
		{
			return $this .' --- '. $this->getTerritoire() .' --- '. $this->getTerritoire()->getRoot();	
		}
		else
		{
			return $this;
		}		
	}
	
	/**
	 * Fourni tous les ancêtres d'un territoire
	 */
	public function getAncestors()
	{
		$ancestors = new ArrayCollection();
		if ( $this->getTerritoire() )
		{
			$ancestors[] = $this->getTerritoire();
			$ancestors = new ArrayCollection(array_merge($ancestors->toArray(),$this->getTerritoire()->getAncestors()->toArray()));
		}
		return $ancestors;
	}
		
	/**
	 * Calcule le nombre d'étape necessaire pour revenir au parent le plus ancien
	 */
	public function stepCount($count = 0)
	{
		if ( $this->getTerritoire() )
		{
			return $this->getTerritoire()->stepCount($count+1);
		}
		return $count;
	}
	
	/**
	 * Fourni la langue principale du territoire
	 */
	public function getLanguePrincipale()
	{
		return $this->getLangue();
	}
	
	/**
	 * Défini la langue principale du territoire
	 * @param Langue $langue
	 */
	public function setLanguePrincipale(Langue $langue)
	{
		return $this->setLangue($langue);
	}
	
	/**
	 * Fourni la religion principale du territoire
	 */
	public function getReligionPrincipale()
	{
		return $this->getReligion();
	}
	
	/**
	 * Défini la religion principale d'un territoire
	 * @param Religion $religion
	 */
	public function setReligionPrincipale(Religion $religion)
	{
		return $this->setReligion($religion);
	}
	
	/**
	 * Fourni le nom complet d'un territoire
	 */
	public function getNomTree()
	{
		$string = $this->getNom();
	
		if ( $this->getTerritoires()->count() != 0 )
		{
			$string .= ' > ';
			$string .= implode(', ', $this->getTerritoires()->toArray());
		}
	
		return $string;
	}
		
	/**
	 * Fourni le nom complet d'un territoire
	 */
	public function getNomComplet()
	{
		$string = $this->getNom();
		
		if ( $this->getGroupe() )
		{
			$string .= ' ( Appartient à #'.$this->getGroupe()->getNumero(). ' ' .$this->getGroupe()->getNom() . ' )';			
		}
				
		return $string;
	}
	
	/**
	 * Add Ressource entity to collection.
	 *
	 * @param \LarpManager\Entities\Ressource $ressource
	 * @return \LarpManager\Entities\Territoire
	 */
	public function addExportation(Ressource $ressource)
	{
		$ressource->addExportateur($this);
		$this->exportations[] = $ressource;
	
		return $this;
	}
	
	/**
	 * Remove Ressource entity from collection.
	 *
	 * @param \LarpManager\Entities\Ressource $ressource
	 * @return \LarpManager\Entities\Territoire
	 */
	public function removeExportation(Ressource $ressource)
	{
		$ressource->removeExportateur($this);
		$this->exportations->removeElement($ressource);
	
		return $this;
	}
	
	/**
	 * Get Ressource entity collection.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getExportations()
	{
		return $this->exportations;
	}
	
	/**
	 * Add Ressource entity to collection.
	 *
	 * @param \LarpManager\Entities\Ressource $ressource
	 * @return \LarpManager\Entities\Territoire
	 */
	public function addImportation(Ressource $ressource)
	{
		$ressource->addImportateur($this);
		$this->importations[] = $ressource;
	
		return $this;
	}
	
	/**
	 * Remove Ressource entity from collection.
	 *
	 * @param \LarpManager\Entities\Ressource $ressource
	 * @return \LarpManager\Entities\Territoire
	 */
	public function removeImportation(Ressource $ressource)
	{
		$ressource->removeImportateur($this);
		$this->importations->removeElement($ressource);
	
		return $this;
	}
	
	/**
	 * Get Ressource entity collection.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getImportations($rarete = null)
	{
		if ( $rarete )
		{
			$importations = new ArrayCollection();
			foreach ( $this->importations as $ressource )
			{
				if ( $ressource->getRarete()->getLabel() == $rarete )
				{
					$importations[] = $ressource;
				}
			}	
			return $importations;
		}
		return $this->importations;
	}
	
	/**
	 * Add Langue entity to collection.
	 *
	 * @param \LarpManager\Entities\Langue $langue
	 * @return \LarpManager\Entities\Territoire
	 */
	public function addLangue(Langue $langue)
	{
		$langue->addTerritoireSecondaire($this);
		$this->langues[] = $langue;
	
		return $this;
	}
	
	/**
	 * Remove Langue entity from collection.
	 *
	 * @param \LarpManager\Entities\Langue $langue
	 * @return \LarpManager\Entities\Territoire
	 */
	public function removeLangue(Langue $langue)
	{
		$langue->removeTerritoireSecondaire($this);
		$this->$langues->removeElement($langue);
	
		return $this;
	}
	
	/**
	 * Get Langue entity collection.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getLangues()
	{
		return $this->langues;
	}
	
	/**
	 * Ajoute une religion dans la collection de religion
	 *
	 * @param \LarpManager\Entities\Religion $religion
	 * @return \LarpManager\Entities\Territoire
	 */
	public function addReligion(Religion $religion)
	{
		$religion->addTerritoireSecondaire($this);
		$this->religions[] = $religion;
	
		return $this;
	}
	
	/**
	 * Retire une religion de la collection de religion
	 *
	 * @param \LarpManager\Entities\Religion $religion
	 * @return \LarpManager\Entities\Territoire
	 */
	public function removeReligion(Religion $religion)
	{
		$religion->removeTerritoireSecondaire($this);
		$this->religions->removeElement($religion);
	
		return $this;
	}
	
	/**
	 * Fourni la collection de religions
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getReligions()
	{
		return $this->religions;
	}
	
	public function setGroupeNull()
	{
		$this->groupe = null;
		return $this;
	}
	
	public function getGroupesFull()
	{
		$groupes = array();
		if ( $this->getGroupe() )
		{
			$groupes[] = $this->getGroupe();
		}
		foreach ( $this->getTerritoires() as $territoire )
		{
			$groupes = array_merge($groupes,$territoire->getGroupesFull());
		}
	
		return array_unique($groupes);
	}

	/**
	 * Fourni le nom de tous les groupes présents dans ce territoire
	 */ 
	public function getGroupes()
	{
		$groupes = array();
		if ( $this->getGroupe() )
		{
			$groupes[] = $this->getGroupe()->getNom();
		}
		foreach ( $this->getTerritoires() as $territoire )
		{
			$groupes = array_merge($groupes,$territoire->getGroupes());
		}
	
		return array_unique($groupes);
	}
	
	/**
	 * Fourni le nom de tous les groupes de PJ présents dans ce territoire
	 */
	public function getGroupesPj()
	{
		$groupes = array();
		if ( $this->getGroupe() )
		{
			if ( $this->getGroupe()->getPj() )
				$groupes[] = $this->getGroupe()->getNom();
		}
		foreach ( $this->getTerritoires() as $territoire )
		{
			$groupes = array_merge($groupes,$territoire->getGroupesPj());
		}
	
		return array_unique($groupes);
	}
	
	/**
	 * Fourni l'indicateur d'ordre/désordre
	 */
	public function getStatutIndex()
	{
		switch ( $this->getStatut())
		{
			case 'Normal' :
				return 0;
			case 'Désordre' :
				return 1;
			case 'Désolation' :
				return 2;
			default :
				return 0;
		}
	}
}