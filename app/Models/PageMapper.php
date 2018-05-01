<?php
/**
 * Page Mapper
 */
namespace Piton\Models;

class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = ['title', 'url', 'url_locked', 'meta_description', 'template'];

    // Get page headers, and optional pagelet content in one DB query
    protected $PageOuterJoinPageletSql = <<<'SQL'
select p.*, pl.id pagelet_id, pl.name, pl.content
from page p left outer join pagelet pl on p.id = pl.page_id
SQL;

    /**
     * Get Pages, and Pagelets
     *
     * Returns multidimensional array of header page fields, and child pagelet array
     * @return array
     */
    public function findPageSets()
    {
        // Get page headers, and pagelet content in one DB query
        $this->sql = $this->PageOuterJoinPageletSql . ' order by p.id, pl.name;';

        $results = $this->find();

        return $this->buildPageAndPageletArray($results);
    }

    /**
     * Get Single Pages, and Pagelets by URL
     *
     * Returns multidimensional array of header page fields, and child pagelet array
     * @param string, /URL
     * @return array
     */
    public function findPageSetById($url)
    {
        // Get page headers, and pagelet content in one DB query
        $this->sql = $this->PageOuterJoinPageletSql . ' where p.url = ?';
        $this->bindValues[] = $url;

        $results = $this->find();

        // Return just the first element of this array
        return reset($this->buildPageAndPageletArray($results));
    }

    /**
     * Construct Page & Pagelet Array
     *
     * Build multi-dimensional array of Page(s) with optional sub-array of Pagelet(s)
     * @param array, Result output from $this->PageOuterJoinPageletSql
     * @return array
     */
    protected function buildPageAndPageletArray($queryResults)
    {
        // Rebuild array into multidimensional array
        $pages = [];
        foreach ($queryResults as $row) {
            if (!array_key_exists($row->id, $pages)) {
                $pages[$row->id] = [
                    'id' => $row->id,
                    'title' => $row->title,
                    'url' => $row->url,
                    'template' => $row->template];
                if ($row->pagelet_id) {
                    $pages[$row->id]['pagelets'][] = [
                        'pagelet_id' => $row->pagelet_id,
                        'name' => $row->name,
                        'content' => $row->content];
                }
            } else {
                $pages[$row->id]['pagelets'][] = [
                    'pagelet_id' => $row->pagelet_id,
                    'name' => $row->name,
                    'content' => $row->content];
            }
        }

        return $pages;
    }
}
