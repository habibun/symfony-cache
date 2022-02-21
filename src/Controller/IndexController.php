<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Exception\CacheException;
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
        return $this->render('index/index.html.twig', []);
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
        $redisUrl = $this->getParameter('cache')['redis'];
        $client = RedisAdapter::createConnection($redisUrl);
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


    /**
     * @Route("/memcached", name="cache_memcached")
     *
     * @throws CacheException|InvalidArgumentException
     */
    public function memcachedAction(): Response
    {
        $client = MemcachedAdapter::createConnection('memcached://localhost');
        $cache = new MemcachedAdapter($client);
        $cacheKey = md5('123');
        $cachedItem = $cache->getItem($cacheKey);

        if (false === $cachedItem->isHit()) {
            $cachedItem->set($cacheKey, 'some value');
            $cache->save($cachedItem);
        }

        return $this->render('cache/memcached.html.twig', [
            'cache' => [
                'hit' => $cachedItem->isHit(),
            ],
        ]);
    }
}
