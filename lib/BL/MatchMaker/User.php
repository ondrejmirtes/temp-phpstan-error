<?php namespace BL_MatchMaker;

/**
 *  DataStructure Object that holds data for the matchmaking process.
 *  @author preston
 *
 */
class User extends MatchDataAbstract implements iAllData{
    
    protected   $first_name                 = '',
                $last_name                  = '',
                $middle_name                = '',
                $email_address              = '',
                $organization_id            = 0,
                $organization_department_id = 0,
                $employee_id                = '',
                $phone_number               = 0,
                $job_id                     = 0,
                $medstar_user_id            = '',
                $date_of_birth              = '';
    
     // ============================== Setters ==========================       
            
    /**
     * Set the first name value for matching
     * @param string $first_name
     */
    public function setFirstName(string $first_name):void{
        $this->first_name   = $first_name;
    }
    
    /**
     * Set the last name value for matching
     * @param string $first_name
     */
    public function setLastName(string $last_name):void{
        $this->last_name   = $last_name;
    }
    
    /**
     * Set the middle name value for matching
     * @param string $first_name
     */
    public function setMiddleName(string $middle_name):void{
        $this->middle_name   = $middle_name;
    }
    
    /**
     * Set the email address value for matching
     * @param string $email_address
     */
    public function setEmailAddress(string $email_address):void{
        $this->email_address    = $email_address;
    }
    
    /**
     * Set the organization id value for matching
     * @param int $organization_id
     */
    public function setOrganizationId(int $organization_id):void{
        $this->organization_id  = $organization_id;
    }
    
    /**
     * Set the organization department id value for matching
     * @param int $organization_department_id
     */
    public function setOrganizationDepartmentId(int $organization_department_id):void{
        $this->organization_department_id   = $organization_department_id;
    }
    
    /**
     * Set the employee id value for matching
     * @param string $employee_id
     */
    public function setEmployeeId(string $employee_id):void{
        $this->employee_id  = $employee_id;
    }
    
    /**
     * Set the phone number value for matching
     * @param int $phone_number
     */
    public function setPhoneNumber(int $phone_number):void{
        $this->phone_number = $phone_number;
    }
    
    /**
     * Set the job id value for matching
     * @param int $job_id
     */
    public function setJobId(int $job_id):void{
        $this->job_id   = $job_id;
    }
    
    /**
     * Set the medstar user id
     * @param string $medstar_user_id
     */
    public function setMedstarUserId(string $medstar_user_id):void{
        $this->medstar_user_id  = $medstar_user_id;
    }
    
    /**
     * Set the date of birth value for matching
     * @param string $date_of_birth
     */
    public function setDateOfBirth(string $date_of_birth):void{
        $this->date_of_birth    = $date_of_birth;
    }
    
    
    //  ======================================== Getters =======================================
    
    /**
     *  Get the user's first name
     *  @return string
     */
    public function getFirstName():string{
        return $this->first_name;
    }
    
    /**
     *  Get the user's last name
     *  @return string
     */
    public function getLastName():string{
        return $this->last_name;
    }
    
    /**
     *  Get the user's middle name
     *  @return string
     */
    public function getMiddleName():string{
        return $this->middle_name;
    }
    
    /**
     * Get the user's email address
     * @return string
     */
    public function getEmailAddress():string{
        return $this->email_address;
    }
    
    /**
     *  Get the user's organization id
     * @return int
     */
    public function getOrganizationId():int{
        return $this->organization_id;
    }
    
    /**
     * Get the user's organization department id
     * @return int
     */
    public function getOrganizationDepartmentId():int{
        return $this->organization_department_id;
    }
    
    /**
     * Get the user's employee id
     * @return string
     */
    public function getEmployeeId():string{
        return $this->employee_id;
    }
    
    /**
     * Get the user's phone number
     * @return int
     */
    public function getPhoneNumber():int{
        return $this->phone_number;
    }
    
    /**
     * Get the user's job id
     * @return int
     */
    public function getJobId():int{
        return $this->job_id;
    }
    
    /**
     * Get the user's medstar user id
     * @return string
     */
    public function getMedstarUserId():string{
        return $this->medstar_user_id;
    }
    
    /**
     * Get the user's date of birth
     * @return string
     */
    public function getDateOfBirth():string{
        return $this->date_of_birth;
    }
    
    /**
     * Values returned must be protected or public
     * Private values not returned
     * Protected is preferred
     * @return array
     */
    public function getAllValues():array{
        return get_object_vars($this);
    }
}




