<?php

namespace Models\Base;

use \Exception;
use \PDO;
use Models\Build as ChildBuild;
use Models\BuildQuery as ChildBuildQuery;
use Models\Map\BuildTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'builds' table.
 *
 *
 *
 * @method     ChildBuildQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildBuildQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildBuildQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildBuildQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     ChildBuildQuery orderByNote($order = Criteria::ASC) Order by the note column
 * @method     ChildBuildQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildBuildQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildBuildQuery groupById() Group by the id column
 * @method     ChildBuildQuery groupByName() Group by the name column
 * @method     ChildBuildQuery groupByVersion() Group by the version column
 * @method     ChildBuildQuery groupByUrl() Group by the url column
 * @method     ChildBuildQuery groupByNote() Group by the note column
 * @method     ChildBuildQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildBuildQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildBuildQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildBuildQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildBuildQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildBuildQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildBuildQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildBuildQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildBuild findOne(ConnectionInterface $con = null) Return the first ChildBuild matching the query
 * @method     ChildBuild findOneOrCreate(ConnectionInterface $con = null) Return the first ChildBuild matching the query, or a new ChildBuild object populated from the query conditions when no match is found
 *
 * @method     ChildBuild findOneById(int $id) Return the first ChildBuild filtered by the id column
 * @method     ChildBuild findOneByName(string $name) Return the first ChildBuild filtered by the name column
 * @method     ChildBuild findOneByVersion(string $version) Return the first ChildBuild filtered by the version column
 * @method     ChildBuild findOneByUrl(string $url) Return the first ChildBuild filtered by the url column
 * @method     ChildBuild findOneByNote(string $note) Return the first ChildBuild filtered by the note column
 * @method     ChildBuild findOneByCreatedAt(string $created_at) Return the first ChildBuild filtered by the created_at column
 * @method     ChildBuild findOneByUpdatedAt(string $updated_at) Return the first ChildBuild filtered by the updated_at column *

 * @method     ChildBuild requirePk($key, ConnectionInterface $con = null) Return the ChildBuild by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOne(ConnectionInterface $con = null) Return the first ChildBuild matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildBuild requireOneById(int $id) Return the first ChildBuild filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOneByName(string $name) Return the first ChildBuild filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOneByVersion(string $version) Return the first ChildBuild filtered by the version column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOneByUrl(string $url) Return the first ChildBuild filtered by the url column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOneByNote(string $note) Return the first ChildBuild filtered by the note column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOneByCreatedAt(string $created_at) Return the first ChildBuild filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBuild requireOneByUpdatedAt(string $updated_at) Return the first ChildBuild filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildBuild[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildBuild objects based on current ModelCriteria
 * @method     ChildBuild[]|ObjectCollection findById(int $id) Return ChildBuild objects filtered by the id column
 * @method     ChildBuild[]|ObjectCollection findByName(string $name) Return ChildBuild objects filtered by the name column
 * @method     ChildBuild[]|ObjectCollection findByVersion(string $version) Return ChildBuild objects filtered by the version column
 * @method     ChildBuild[]|ObjectCollection findByUrl(string $url) Return ChildBuild objects filtered by the url column
 * @method     ChildBuild[]|ObjectCollection findByNote(string $note) Return ChildBuild objects filtered by the note column
 * @method     ChildBuild[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildBuild objects filtered by the created_at column
 * @method     ChildBuild[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildBuild objects filtered by the updated_at column
 * @method     ChildBuild[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class BuildQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Models\Base\BuildQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Models\\Build', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildBuildQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildBuildQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildBuildQuery) {
            return $criteria;
        }
        $query = new ChildBuildQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildBuild|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(BuildTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = BuildTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildBuild A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name, version, url, note, created_at, updated_at FROM builds WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildBuild $obj */
            $obj = new ChildBuild();
            $obj->hydrate($row);
            BuildTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildBuild|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BuildTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BuildTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BuildTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BuildTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%', Criteria::LIKE); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the version column
     *
     * Example usage:
     * <code>
     * $query->filterByVersion('fooValue');   // WHERE version = 'fooValue'
     * $query->filterByVersion('%fooValue%', Criteria::LIKE); // WHERE version LIKE '%fooValue%'
     * </code>
     *
     * @param     string $version The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($version)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_VERSION, $version, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%', Criteria::LIKE); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_URL, $url, $comparison);
    }

    /**
     * Filter the query on the note column
     *
     * Example usage:
     * <code>
     * $query->filterByNote('fooValue');   // WHERE note = 'fooValue'
     * $query->filterByNote('%fooValue%', Criteria::LIKE); // WHERE note LIKE '%fooValue%'
     * </code>
     *
     * @param     string $note The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByNote($note = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($note)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_NOTE, $note, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(BuildTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(BuildTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(BuildTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(BuildTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BuildTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildBuild $build Object to remove from the list of results
     *
     * @return $this|ChildBuildQuery The current query, for fluid interface
     */
    public function prune($build = null)
    {
        if ($build) {
            $this->addUsingAlias(BuildTableMap::COL_ID, $build->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the builds table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BuildTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            BuildTableMap::clearInstancePool();
            BuildTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BuildTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(BuildTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            BuildTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            BuildTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildBuildQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(BuildTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildBuildQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(BuildTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildBuildQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(BuildTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildBuildQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(BuildTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildBuildQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(BuildTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildBuildQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(BuildTableMap::COL_CREATED_AT);
    }

} // BuildQuery
