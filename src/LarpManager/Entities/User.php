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
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-08-14 09:10:36.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BaseUser;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LarpManager\Entities\User
 *
 * @Entity(repositoryClass="LarpManager\Repository\UserRepository")
 */
class User extends BaseUser implements AdvancedUserInterface, \Serializable
{
	/**
	 * Lors de la création d'un nouvel utilisateur, on lui détermine aléatoirement
	 * la valeur salt.
	 * 
	 * @param string $email
	 */
	public function __construct($email)
	{
		$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
		$this->email = $email;
		$this->setCreationDate(new \Datetime('NOW'));
		parent::__construct();
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getUsername();
	}
		
	/**
	 * Ajoute un coeur
	 */
	public function addCoeur()
	{
		$coeur = $this->getCoeur();
		$coeur += 1;
		$this->setCoeur($coeur);
		return $this;
	}
	
	/**
	 * Recherche si l'utilisateur à un événement futur de prévu
	 */
	public function hasFuturEvent()
	{
		$now = new \Datetime('NOW');
		foreach ( $this->getParticipants() as $participant )
		{
			if ( $participant->getGn()->getDateDebut() > $now )
			{
				return true;
			}
			
		}
		return false;
	}
	
	/**
	 * Fourni la liste de tous les événements futur auquel l'utilisateur participe
	 */
	public function getFuturEvents()
	{
		$futurEvents = new ArrayCollection();
		$now = new \Datetime('NOW');
		
		foreach ( $this->getParticipants() as $participant )
		{
			if ( $participant->getGn()->getDateDebut() > $now )
			{
				$futurEvents[] = $participant;
			}
				
		}
		return $futurEvents;
	}
	
	/**
	 * Fourni les informations de participation à un jeu
	 * 
	 * @param Gn $gn
	 */
	public function getParticipant(Gn $gn)
	{
		foreach ( $this->getParticipants() as $participant )
		{
			if ( $participant->getGn() == $gn )
			{
				return $participant;		
			}
		}
		return null;
	}
		
	
	/**
	 * Fourni tous les billets d'un utilisateur
	 */
	public function getBillets()
	{
		$billets = new ArrayCollection();
		foreach ( $this->getParticipants() as $participant )
		{
			if ( $participant->getBillet() )
			{
				$billets[] = $participant->getBillet();
			}
		}
		return $billets;
	}
	
	/**
	 * Fourni la liste des tous les GN auquel l'utillisateur participe
	 */
	public function getGns()
	{
		$gns = new ArrayCollection();
		foreach ( $this->getParticipants() as $participant )
		{
			$gns[] = $participant->getGn();
		}
		return $gns;
	}
	
	/**
	 * Alias vers username
	 */
	public function getName()
	{
		return $this->getUsername();
	}
	
	/**
	 * username + email
	 */
	public function getIdentity()
	{
		return $this->getUsername() . ' ' . $this->getEmail();
	}
	
	
	/**
	 * Indique si l'utilisateur participe à un GN
	 * 
	 * @param Gn $gn
	 * @return boolean
	 */
	public function takePart(Gn $gn) 
	{
		foreach ( $this->getParticipants() as $participant )
		{
			if ( $participant->getGn() == $gn )
				return true;
		}
		
		return false;
	}
	
	/**
	 * Indique si l'utilisateur est membre d'un groupe
	 * 
	 * @param Groupe $groupe
	 */
	public function isMemberOf(Groupe $groupe)
	{
		return $this->getGroupes()->contains($groupe);
	}
	
	/**
	 * Vérifie si un post est surveillé par l'utilisateur
	 * 
	 * @param \LarpManager\Entities\Post $post
	 * @return boolean
	 */
	public function isInWatchingList(Post $post)
	{
		foreach ($this->getPosts() as $p)
		{
			if ( $post == $p ) return true;
		}
		return false;
	}
	
	/**
	 * TODO Fourni la liste de tous les posts qu'un utilisateur n'a pas lu
	 */
	public function newPosts(Topic $topic)
	{
		$newPosts = new ArrayCollection();
		foreach( $topic->getPosts() as $post)
		{
			if ( ! $this->alreadyView($post))
			{
				$newPosts[] = $post;
			}
		}
		
		return $newPosts;		
	}
	
	/**
	 * Determine si l'utilisateur a déjà lu ce post
	 * 
	 * @param LarpManager\Entities\Post $post
	 */
	public function alreadyView(\LarpManager\Entities\Post $post)
	{
		foreach ( $this->getPostViews() as $postView )
		{
			if ( $postView->getPost() == $post) return true;
		}
		return false;
	}
	
	/**
	 * Determine si l'une des réponses à un post n'a pas été lu par l'utilisateur
	 * 
	 * @param LarpManager\Entities\Post $post
	 */
	public function newResponse(\LarpManager\Entities\Post $post)
	{
		// construit la liste des posts déjà vue
		$alreadyView = new ArrayCollection();
		
		foreach ( $this->getPostViews() as $postView )
		{
			$alreadyView[] = $postView->getPost();
		}
		
		foreach($post->getPosts() as $p)
		{
			if ( ! $alreadyView->contains($p) ) return true;
		}
		return false;
	}
	
	/**
	 * Fourni la liste des groupes dont l'utilisateur est le scénariste
	 */
	public function getGroupeScenariste()
	{
		return $this->getGroupeRelatedByScenaristeIds();	
	}
	
	
	/**
	 * Returns the roles granted to the user. Note that all users have the ROLE_USER role.
	 *
	 * @return array A list of the user's roles.
	 */
	public function getRoles()
	{
		$roles = explode(',',$this->rights);
	
		// Every user must have at least one role, per Silex security docs.
		$roles[] = 'ROLE_USER';
	
		return array_unique($roles);
	}
	
	/**
	 * Set the user's roles to the given list.
	 *
	 * @param array $roles
	 */
	public function setRoles($roles)
	{
		$this->rights = '';
	
		foreach ($roles as $role) {
			$this->addRole($role);
		}
	}
	
	/**
	 * Test whether the user has the given role.
	 *
	 * @param string $role
	 * @return bool
	 */
	public function hasRole($role)
	{
		return in_array(strtoupper($role), $this->getRoles(), true);
	}
	
	/**
	 * Add the given role to the user.
	 *
	 * @param string $role
	 */
	public function addRole($role)
	{
		$role = strtoupper($role);
	
		if ($role === 'ROLE_USER') {
			return;
		}
	
		$roles = explode(',',$this->rights);
		
		if (!$this->hasRole($role)) {
			$roles[] = $role;
		}
		$this->rights = implode(',',array_values($roles));
	}
	
	/**
	 * Remove the given role from the user.
	 *
	 * @param string $role
	 */
	public function removeRole($role)
	{
		$roles = explode(',',$this->rights);
		
		if (false !== $key = array_search(strtoupper($role), $roles, true)) {
			unset($roles[$key]);
			$this->rights = implode(',',array_values($roles));
		}
	}
	
	/**
	 * Returns the username, if not empty, otherwise the email address.
	 *
	 * Email is returned as a fallback because username is optional,
	 * but the Symfony Security system depends on getUsername() returning a value.
	 * Use getRealUsername() to get the actual username value.
	 *
	 * This method is required by the UserInterface.
	 *
	 * @see getRealUsername
	 * @return string The username, if not empty, otherwise the email.
	 */
	public function getUsername()
	{
		return $this->username ?: $this->email;
	}
	
	/**
	 * Get the actual username value that was set,
	 * or null if no username has been set.
	 * Compare to getUsername, which returns the email if username is not set.
	 *
	 * @see getUsername
	 * @return string|null
	 */
	public function getRealUsername()
	{
		return $this->username;
	}
	
	/**
	 * The Symfony Security component stores a serialized User object in the session.
	 * We only need it to store the user ID, because the user provider's refreshUser() method is called on each request
	 * and reloads the user by its ID.
	 *
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize(array(
				$this->id,
		));
	}
	
	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		list (
				$this->id,
				) = unserialize($serialized);
	}
	

	/**
	 * Checks whether the user is enabled.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a DisabledException and prevent login.
	 *
	 * Users are enabled by default.
	 *
	 * @return bool    true if the user is enabled, false otherwise
	 *
	 * @see DisabledException
	 */
	public function isEnabled()
	{
		return $this->isEnabled;
	}
	
	
	public function setEnabled($isEnabled)
	{
		return $this->setIsEnabled($isEnabled);
	}
	
	/**
	 * @param int $ttl Password reset request TTL, in seconds.
	 * @return bool
	 */
	public function isPasswordResetRequestExpired($ttl)
	{
		$timeRequested = $this->getTimePasswordResetRequested();
		if (!$timeRequested) {
			return true;
		}
		return $timeRequested + $ttl < time();
	}
	
	/**
	 * Checks whether the user's credentials (password) has expired.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a CredentialsExpiredException and prevent login.
	 *
	 * @return bool    true if the user's credentials are non expired, false otherwise
	 *
	 * @see CredentialsExpiredException
	 */
	public function isCredentialsNonExpired()
	{
		return true;
	}
	
	/**
	 * Checks whether the user is locked.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a LockedException and prevent login.
	 *
	 * @return bool    true if the user is not locked, false otherwise
	 *
	 * @see LockedException
	 */
	public function isAccountNonLocked()
	{
		return true;
	}
	
	/**
	 * Checks whether the user's account has expired.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw an AccountExpiredException and prevent login.
	 *
	 * @return bool    true if the user's account is non expired, false otherwise
	 *
	 * @see AccountExpiredException
	 */
	public function isAccountNonExpired()
	{
		return true;
	}
	
	public function equals(User $user)
	{
		
	}
	
	/**
	 * Removes sensitive data from the user.
	 *
	 * This is a no-op, since we never store the plain text credentials in this object.
	 * It's required by UserInterface.
	 *
	 * @return void
	 */
	public function eraseCredentials()
	{
		
	}
	
	/**
	 * Returns the name, if set, or else "Anonymous {id}".
	 *
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->username ?: 'Anonymous ' . $this->id;
	}
	
	/**
	 * Test whether username has ever been set (even if it's currently empty).
	 *
	 * @return bool
	 */
	public function hasRealUsername()
	{
		return !is_null($this->username);
	}
	
	/**
	 * Validate the user object.
	 *
	 * @return array An array of error messages, or an empty array if there were no errors.
	 */
	public function validate()
	{
		$errors = array();
	
		if (!$this->getEmail()) {
			$errors['email'] = 'Email address is required.';
		} else if (!strpos($this->getEmail(), '@')) {
			// Basic email format sanity check. Real validation comes from sending them an email with a link they have to click.
			$errors['email'] = 'Email address appears to be invalid.';
		} else if (strlen($this->getEmail()) > 100) {
			$errors['email'] = 'Email address can\'t be longer than 100 characters.';
		}
	
		if (!$this->getPassword()) {
			$errors['password'] = 'Password is required.';
		} else if (strlen($this->getPassword()) > 255) {
			$errors['password'] = 'Password can\'t be longer than 255 characters.';
		}
	
		if (strlen($this->getUsername()) > 100) {
			$errors['name'] = 'Name can\'t be longer than 100 characters.';
		}
	
		// Username can't contain "@",
		// because that's how we distinguish between email and username when signing in.
		// (It's possible to sign in by providing either one.)
		if ($this->getRealUsername() && strpos($this->getRealUsername(), '@') !== false) {
			$errors['username'] = 'Username cannot contain the "@" symbol.';
		}
	
		return $errors;
	}
	
	public function getLastPersonnage() {
	    $last = null;
	    foreach($this->getParticipants() as $participant) {
		if($participant->getPersonnage() != null) {
			if($last == null || $participant->getId() > $last->getId()) {
			    // On conserve la dernière participation avec un personnage
			    $last = $participant;
			}
		}
	    }

	    return $last->getPersonnage();
	}
}
