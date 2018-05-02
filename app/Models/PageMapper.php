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
            // Build pagelet
            if ($row->pagelet_id) {
                $pagelet = new \stdClass();
                $pagelet->id = $row->pagelet_id;
                $pagelet->name = $row->name;
                $pagelet->content = $row->content;

                // Assign pagelet to parent
                $row->pagelets = [];
                $row->pagelets[$pagelet->name] = $pagelet;

                // Clean up parent row object
                unset($row->pagelet_id);
                unset($row->name);
                unset($row->content);
            }

            // Check if this page exists in the return array
            if (!array_key_exists($row->id, $pages)) {
                // Assign page to return array
                $pages[$row->id] = $row;
            } else {
                // Assign pagelet to parent page
                $pages[$row->id]->pagelets[$pagelet->name] = $pagelet;
            }
        }

        return $pages;
    }
}
