<?php
namespace Drupal\hello\Access;
 
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;


class helloAccessCheck implements AccessInterface {
 
  public function applies(Route $route){
  	return NULL;

  }

  public function access(Route $route, Request $request = NULL, AccountInterface $account) {
      
    $nb_heures = $route->getRequirement('_access_hello');


    if($account->isAnonymous()){
    	return AccessResult::forbidden();
      }


     if ((time() - $account->getAccount()->created > $nb_heures * 3600 )) {
     	return AccessResult::allowed()->cachePerUser()->setCacheMaxAge(60);
     }
    
    return AccessResult::forbidden();
  }
 
}