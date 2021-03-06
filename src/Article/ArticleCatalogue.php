<?php

namespace App\Article;

use App\Article\Source\ArticleAbstractSource;
use App\Entity\Article;
use App\Exception\DuplicateCatalogueArticleException;
use Tightenco\Collect\Support\Collection;

class ArticleCatalogue implements ArticleCatalogueInterface
{
    private $sources;

    /**
     * @param \App\Article\Source\ArticleAbstractSource $source
     */
    public function addSource(ArticleAbstractSource $source): void
    {
        // TODO: Vérifier que la source n'est pas déjà présente.
        $this->sources[] = $source;
    }

    /**
     * @param iterable $sources
     */
    public function setSources(iterable $sources): void
    {
        $this->sources = $sources;
    }

    /**
     * @return iterable
     */
    public function getSources(): iterable
    {
        return $this->sources;
    }

    /**
     * Permet de retourner un article sur la
     * base de son identifiant unique.
     *
     * @param $id
     *
     * @return Article|null
     */
    public function find($id): ?Article
    {
        $articles = new Collection();

        // Je parcours mes sources à la recherche de mon article
        /* @var $source ArticleAbstractSource */
        foreach ($this->sources as $source) {
            // J'appel la fonction find() de chaque source
            $article = $source->find($id);

            /*
             * Si ma source ne me renvoi pas null,
             * alors je l'ajoute à ma liste d'articles
             */
            if (null !== $article) {
                $articles[] = $article;
            }

            // Vérification s'il y a des doublons
            //if($articles->count() > 1) {
            //    throw new DuplicateCatalogueArticleException(sprintf(
            //       'Return value of %s cannot return more than one record on line %s',
            //       get_class($this).'::'.__FUNCTION__.'()',__LINE__
            //    ));
            //}
        }

        // Retourne l'article de la dernière source
        return $articles->pop();
    }

    /**
     * Retourne la liste de tous les articles.
     *
     * @return iterable|null
     */
    public function findAll(): ?iterable
    {
        return $this->iterateOverSources('findAll')
            ->sortBy('createdDate');
    }

    /**
     * Retourne les 5 derniers articles depuis
     * l'ensemble de nos sources...
     *
     * @return iterable|null
     */
    public function findLastFiveArticles(): ?iterable
    {
        /*
         * TODO : Voir le Tri par date
         */
        return $this->iterateOverSources('findLastFiveArticles')
            ->slice(-5);
    }

    /**
     * Retourne le nombre d'éléments de chaque source.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->sources);
    }

    private function iterateOverSources(string $functionToCall): Collection
    {
        $articles = new Collection();

        /* @var $source ArticleAbstractSource */
        /* @var $article Article */
        foreach ($this->sources as $source) {
            foreach ($source->$functionToCall() as $article) {
                $articles[] = $article;
            }
        }

        return $articles;
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param null       $limit
     * @param null       $offset
     */
    public function findBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        // TODO: Implement findBy() method.
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria the criteria
     */
    public function findOneBy(array $criteria)
    {
        // TODO: Implement findOneBy() method.
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    /**
     * Récupère des statistiques sur les différentes sources.
     */
    public function getStats()
    {
        $stats = [];

        /*
         * @var ArticleAbstractSource
         */
        foreach ($this->sources as $source) {
            $stats[get_class($source)] = $source->count();
        }

        return $stats;
    }
}
