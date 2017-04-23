<?php

	namespace AppBundle\Security;

	use AppBundle\Form\LoginForm;
	use Doctrine\ORM\EntityManager;
	use Symfony\Component\Form\FormFactoryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\RouterInterface;
	use Symfony\Component\Security\Core\Security;
	use Symfony\Component\Security\Core\User\UserInterface;
	use Symfony\Component\Security\Core\User\UserProviderInterface;
	use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
	use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

	class LoginFormAuthenticator extends AbstractFormLoginAuthenticator 
	{
		private $formFactory;
		private $em;
		private $router;
		private $passwordEncoder;

		public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder, TokenStorageInterface $tokenStorage)
		{
			$this->formFactory = $formFactory;
			$this->em = $em;
			$this->router = $router;
			$this->passwordEncoder = $passwordEncoder;
			$this->tokenStorage = $tokenStorage;
		}

		public function getCredentials(Request $request)
	    {	
	    	// Check if path is /login and method is POST
	    	$loginAttempt = $request->getPathInfo() == '/login' && $request->isMethod('POST');

	    	if(!$loginAttempt) {
	    		return null;
	    	}

	    	// Process form
	    	$form = $this->formFactory->create(LoginForm::class);
	    	$form->handleRequest($request);
	    	$data = $form->getData();

	    	$request->getSession()->set(
	    		Security::LAST_USERNAME,
	    		$data['_username']
	    	);

	    	return $data;
	    }

	    public function getUser($credentials, UserProviderInterface $userProvider)
	    {
	    	// Get the user from db
	    	$username = $credentials['_username'];

	    	return $this->em->getRepository('AppBundle:User')
	    	->findOneBy(['email' => $username]);

	    }

	    public function checkCredentials($credentials, UserInterface $user)
	    {
	    	$password = $credentials['_password'];

	    	if($this->passwordEncoder->isPasswordValid($user, $password)) {
				return true;
			}

	    	return false;
	    }

	    protected function getLoginUrl()
	    {
	    	// Redirect user to login if auth is failed
	    	return $this->router->generate('auth_login');
	    }

	    protected function getDefaultSuccessRedirectUrl()
	    {	
	    	// Get user role
	    	$roles = $this->tokenStorage->getToken()->getUser()->getRoles();

	    	// Redirect based on roles
	    	if(in_array('ROLE_USER', $roles)) {
	    		return $this->router->generate('home');
	    	}
	    	if(in_array('ROLE_ADMIN', $roles)) {
	    		return $this->router->generate('dashboard');
	    	} elseif(in_array('ROLE_EDITOR', $roles)) {
	    		return $this->router->generate('dashboard');
	    	}
	    	
		}
	}
?>