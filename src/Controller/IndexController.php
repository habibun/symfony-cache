<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route(name="index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/cache-file", name="cache_file")
     */
    public function cacheFileAction()
    {
        $cache = new FilesystemAdapter();
        $cachedData = $cache->getItem('random_number');

        if ($cachedData->isHit()) {
            return new JsonResponse([
                'data' => $cachedData->get(),
                'hit' => $cachedData->isHit(),
            ]);
        }

        $number = rand(1, 100);

        $cachedData->set($number);
        $cachedData->expiresAfter(10);

        $cache->save($cachedData);

        return new JsonResponse([
            'data' => $cachedData->get(),
            'hit' => $cachedData->isHit(),
        ]);
    }

    /**
     * @Route("/cache-redis", name="cache_redis")
     */
    public function cacheRedisAction()
    {
        $client = RedisAdapter::createConnection('redis://172.23.0.2:6379');
        $cache = new RedisAdapter($client, $namespace = '', $defaultLifetime = 0);
        $cacheKey = '123';
        $cachedItem = $cache->getItem($cacheKey);

        if (false === $cachedItem->isHit()) {
            $cachedItem->set($cacheKey, 'some value');
            $cache->save($cachedItem);
        }

        return $this->render('default/index.html.twig', [
            'cache' => [
                'hit' => $cachedItem->isHit(),
            ],
        ]);
    }
}
