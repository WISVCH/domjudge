<?php declare(strict_types=1);

namespace DOMJudgeBundle\Controller\API;

use Doctrine\ORM\EntityManagerInterface;
use DOMJudgeBundle\Entity\User;
use DOMJudgeBundle\Entity\Team;
use DOMJudgeBundle\Entity\HostJudge;
use DOMJudgeBundle\Entity\HostTeam;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Rest\Route("/api/v4", defaults={ "_format" = "json" })
 * @Rest\Prefix("/api")
 * @Rest\NamePrefix("chipcie_")
 * @SWG\Tag(name="CHipCie")
 * @SWG\Response(response="404", ref="#/definitions/NotFound")
 */
class CHipCieController extends FOSRestController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * CHipCieTeamController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get the teamname based on the current or specified ip
     * @param Request $request
     * @return string
     * @Rest\Get("/team")
     * @SWG\Get("/team")
     * @SWG\Parameter(
     *     name="ip",
     *     in="query",
     *     type="string",
     *     description="Show the teamname for the specified ip",
     *     required=false
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Returns the teamname based the current or specified ip",
     *     @SWG\Schema(
     *         type="string"
     *     )
     * )
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTeamName(Request $request)
    {
        $ip = $request->query->has('ip') ? $request->query->get('ip') : $request->getClientIp();
        $team = $this->em->createQuery('SELECT t.name FROM DOMJudgeBundle:Team t LEFT JOIN DOMJudgeBundle:User u WITH (t.teamid = u.teamid) WHERE u.ipAddress = :ip')
                ->setParameter(':ip', $ip)
                ->getOneOrNullResult();
        if ($team === null) {
            throw new NotFoundHttpException("No team found for ip: '$ip'");
        }
        return $team['name'];
    }

    /**
     * Get the host id based on the current ip
     * @param Request $request
     * @return integer
     * @Rest\Get("/hostname")
     * @SWG\Get("/hostname")
     * @SWG\Parameter(
     *     name="judgehost",
     *     in="query",
     *     type="boolean",
     *     description="Whether to treat this host as a teampc or judgehost"
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Returns the host id based the current ip",
     *     @SWG\Schema(
     *         type="integer"
     *     )
     * )
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHostname(Request $request)
    {
        $table = $request->query->has('judgehost') && $request->query->get('judgehost') != false ? "Judge" : "Team";
        $ip = $request->getClientIp();
        $host = $this->em->createQuery("SELECT h FROM DOMJudgeBundle:Host$table h WHERE h.ip_address = :ip")
                ->setParameter(':ip', $ip)
                ->getOneOrNullResult();
        if ($host === null) {
            $hostClass = "\DOMJudgeBundle\Entity\Host$table";
            $host = new $hostClass();
            $host->setIpAddress($ip);
            $this->em->persist($host);
            $this->em->flush();
        }
        return $host->getId();
    }
}
