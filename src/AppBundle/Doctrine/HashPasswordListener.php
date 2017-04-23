<?php
	
	namespace AppBundle\Doctrine;

	use Doctrine\Common\EventSubscriber;
	use Doctrine\ORM\Event\LifecycleEventArgs;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

	class HashPasswordListener implements EventSubscriber
	{	

		private $passwordEncoder;

		public function __construct(UserPasswordEncoder $passwordEncoder) 
		{

		} 

		public function prePersist(LifecycleEventArgs)
		{
			$entity = $args->getEntity();
			if(!$entity instanceof User) {
				return null;
			}

			// Encode the password
			$encodedPass = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword);
			$entity->setPassword($encodedPass);
		}

		public function preUpdate(LifecycleEventArgs)
		{
			$entity = $args->getEntity();
			if(!$entity instanceof User) {
				return null;
			}

			// Encode the password
			$encodedPass = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword);
			$entity->setPassword($encodedPass);

			// force the update to see the change
	        $em = $args->getEntityManager();
	        $meta = $em->getClassMetadata(get_class($entity));
			$em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
		}

		public function getSubscribedEvents() 
		{
			return array ['prePersist', 'preUpdate'];
		}

		/**
	    * @param User $entity
	    */
	    private function encodePassword(User $entity)
	    {
	        if (!$entity->getPlainPassword()) {
	            return;
	        }

	        $encoded = $this->passwordEncoder->encodePassword(
	            $entity,
	            $entity->getPlainPassword()
	        );
	        
	        $entity->setPassword($encoded);
	}

	}

?>