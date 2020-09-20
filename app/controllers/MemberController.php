<?php
declare(strict_types=1);

 

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use tennisClub\Member;



class MemberController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
    }

    /**
     * Searches for Member
     */
    public function searchAction()
    {

        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, '\tennisClub\Member', $_GET)->getParams();
        $parameters['order'] = "id";
        $paginator   = new PaginatorModel(
            [
                'model'      => Member::class,
                'parameters' => $parameters,
                'limit'      => 10,
                'page'       => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any Member");

            $this->dispatcher->forward([
                "controller" => "Member",
                "action" => "index"
            ]);

            return;
        }

        $this->view->setVar('page',$paginate);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        //
    }

    /**
     * Edits a Member
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $member = Member::findFirstByid($id);
            if (!$member) {
                $this->flash->error("Member was not found");

                $this->dispatcher->forward([
                    'controller' => "Member",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $member->getId();

            $this->tag->setDefault("id", $member->getId());
            $this->tag->setDefault("firstname", $member->getFirstname());
            $this->tag->setDefault("surname", $member->getSurname());
            $this->tag->setDefault("membertype", $member->getMembertype());
            $this->tag->setDefault("dateofbirth", $member->getDateofbirth());
            
        }
    }

    /**
     * Creates a new Member
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'index'
            ]);

            return;
        }

        $member = new Member();
        $member->setfirstname($this->request->getPost("firstname", "int"));
        $member->setsurname($this->request->getPost("surname", "int"));
        $member->setmembertype($this->request->getPost("membertype", "int"));
        $member->setdateofbirth($this->request->getPost("dateofbirth", "int"));
        

        if (!$member->save()) {
            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("Member was created successfully");

        $this->dispatcher->forward([
            'controller' => "Member",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a Member edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $member = Member::findFirstByid($id);

        if (!$member) {
            $this->flash->error("Member does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'index'
            ]);

            return;
        }

        $member->setfirstname($this->request->getPost("firstname", "int"));
        $member->setsurname($this->request->getPost("surname", "int"));
        $member->setmembertype($this->request->getPost("membertype", "int"));
        $member->setdateofbirth($this->request->getPost("dateofbirth", "int"));
        

        if (!$member->save()) {

            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'edit',
                'params' => [$member->getId()]
            ]);

            return;
        }

        $this->flash->success("Member was updated successfully");

        $this->dispatcher->forward([
            'controller' => "Member",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a Member
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $member = Member::findFirstByid($id);
        if (!$member) {
            $this->flash->error("Member was not found");

            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'index'
            ]);

            return;
        }

        if (!$member->delete()) {

            foreach ($member->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "Member",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("Member was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "Member",
            'action' => "index"
        ]);
    }
}
