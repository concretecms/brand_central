<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Express;

class Search extends PageController
{
    public function view()
    {
        $query = $this->request->query;

        $input = $query->get('keywords');
        $filter = $query->get('filter');
        $tags = $this->extractTags($query->get('tags'));
        $itemsPerPage = $query->get('ipp');
        $filterLabel = 'Anything';

        $showCollectionResults = false;
        if ($input) {
            $collectionEntity = Express::getObjectByHandle('collection');
            $collectionList = new EntryList($collectionEntity);
            $collectionList->filterByKeywords($input);
            $collectionList->setItemsPerPage(3);

            $collectionPaginationFactory = new PaginationFactory($this->request);
            $collectionPagination = $collectionPaginationFactory->createPaginationObject($collectionList);
            $collectionResults = $collectionPagination->getCurrentPageResults();

            if ($collectionResults && count($collectionResults) > 0) {
                if ((!$query->has('ccm_paging_p')) || intval($query->get('ccm_paging_p')) < 2) {
                    $showCollectionResults = true;
                }
            }
        }

        switch ($filter) {
            case 'photo' :
                $filterLabel = 'Photos';
                break;
            case 'logo':
                $filterLabel = 'Logos';
                break;
            case 'video':
                $filterLabel = 'Videos';
                break;
            case 'template':
                $filterLabel = 'Templates';
                break;
        }

        $keywords = $this->extractKeywords($input);

        $entity = Express::getObjectByHandle('asset');
        $list = new \Concrete\Core\Express\EntryList($entity);

        if ($keywords) {
            foreach ($keywords as $key) {
                $list->filterByKeywords($key);
            }
        } else {
            if ($tags) {
                foreach ($tags as $tag) {
                    $list->filterByAssetTags($tag);
                }
                $keywords = $tags;
            }
        }

        $list->filterByAssetType($filter);
        $list->ignorePermissions();

        if (!$itemsPerPage) {
            $itemsPerPage = 12;
        }

        $list->setItemsPerPage($itemsPerPage);

        $sort = $query->get('sortBy');
        if (!in_array($sort, ['name', 'recent', 'oldest'])) {
            $sort = 'recent';
        }

        switch ($sort) {
            case 'name':
                $list->sortByAssetName('asc');
                break;
            case 'oldest':
                $list->sortByDateAdded();
                break;
            case 'recent':
                $list->sortByDateAddedDescending();
                break;
        }

        if ($keywords) {
            $qKeywords = implode(", ", $keywords);
        } else {
            $qKeywords = '';
        }

        $paginationFactory = new PaginationFactory($this->request);
        $pagination = $paginationFactory->createPaginationObject($list);
        $searchResults = $pagination->getCurrentPageResults();

        $count = $list->getTotalResults();
        $this->set("pagination", $pagination);
        $this->set("input_search", $input);
        $this->set("count_results", $count);
        $this->set("filter", $filter);
        $this->set("filter_label", $filterLabel);
        $this->set('search_assets', $searchResults);
        $this->set('search_tag', $tag);
        $this->set('keywords', $keywords);
        $this->set('sortBy', $sort);
        $this->set('qkeywords', $qKeywords);
        $this->set('items_per_page', $itemsPerPage);
        $this->set('collectionResults', $collectionResults);
        $this->set('showCollectionResults', $showCollectionResults);
    }

    /**
     * @param mixed $tags
     * @return array
     */
    protected function extractTags($tags) : array
    {
        $return = [];
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $tag = preg_replace('/[<>"\']/', '', $tag);
                if ($tag) {
                    $return[] = $tag;
                }
            }
        }
        return $return;
    }

    public function getRemoveTagUrl($keyword)
    {
        $url = \URL::to('/search');
        $query = $this->request->query;
        $new = [];
        if ($query->has('tags')) {
            $new['tags'] = [];
            foreach ($query->get('tags') as $tag) {
                if ($tag != $keyword) {
                    $new['tags'][] = $tag;
                }
            }
        }
        $explodedKeywords = explode(',', $query->get('keywords'));
        $newKeywords = [];
        foreach ($explodedKeywords as $explodedKeyword) {
            $explodedKeyword = trim($explodedKeyword);
            if ($explodedKeyword != $keyword) {
                $newKeywords[] = $explodedKeyword;
            }
        }
        $new['keywords'] = implode(', ', $newKeywords);
        $url = $url->setQuery($new);
        return $url;
    }

    private function extractKeywords($string)
    {
        mb_internal_encoding('UTF-8');
        $stopwords = array(
            'i',
            'a',
            'about',
            'an',
            'and',
            'are',
            'as',
            'at',
            'be',
            'by',
            'com',
            'de',
            'en',
            'for',
            'from',
            'how',
            'in',
            'is',
            'it',
            'la',
            'of',
            'on',
            'or',
            'that',
            'the',
            'this',
            'to',
            'was',
            'what',
            'when',
            'where',
            'who',
            'will',
            'with',
            'und',
            'the',
            'www'
        );
        $string = preg_replace('/[\pP]/', '', trim(preg_replace('/\s\s+/i', '', mb_strtolower(utf8_encode($string)))));
        $matchWords = array_filter(explode(' ', $string), function ($item) use ($stopwords) {
            return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) < 2 || is_numeric($item));
        });
        $wordCountArr = array_count_values($matchWords);
        arsort($wordCountArr);
        return array_keys(array_slice($wordCountArr, 0, 10));
    }
}
