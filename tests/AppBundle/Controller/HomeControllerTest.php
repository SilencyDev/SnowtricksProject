<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
  private $client = null;
  
  public function setUp()
  {
    $this->client = static::createClient();
  }
  
  public function testHomepageIsUp()
  {
    $this->client->request('GET', '/');
    
    static::assertEquals(200, $this->client->getResponse()->getStatusCode());
  }
}