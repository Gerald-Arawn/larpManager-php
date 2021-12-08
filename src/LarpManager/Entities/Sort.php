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
 * Version 2.1.6-dev (doctrine2-annotation) on 2016-04-22 11:35:06.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BaseSort;

/**
 * LarpManager\Entities\Sort
 *
 * @Entity(repositoryClass="LarpManager\Repository\SortRepository")
 */
class Sort extends BaseSort
{
	public function getFullLabel()
	{
		return $this->getLabel() .' - '. $this->getDomaine()->getLabel() .' Niveau '. $this->getNiveau();
	}

	public function getPrintLabel()
	{
		return preg_replace('/[^a-z0-9]+/', '_', strtolower($this->getLabel() .'_'. $this->getDomaine()->getLabel() .'_'. $this->getNiveau()));
	}	
}