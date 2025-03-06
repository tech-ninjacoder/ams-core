### Department

List:
 
URL: ```app/departments```(GET) 

Create: 

URL: ```app/departments``` (POST)

Data: 
```
{
    'name' => 'required',
    'manager_id' => 'optional',
    'department_id' => ''
}
```


Show: ```app/departments/{id}``` (GET)

Update: 

URL: ```app/departments/{id}``` (PATCH)

Data: 

```
{
    'name' => 'required',
    'manager_id' => 'optional',
    'department_id' => ''
}
```

Delete: 

URL: ```app/departments/{id}``` (DELETE)

### Selectable Department API

```selectable/departments```


### Employment Status

Url ```app/employment-statuses``` Follow the laravel resource url structure 

Data: 

```
{
   name: '',
   class: ''
}
```

### Employee API

URL: ``app/employees``

### Employee auto id

URL: ``employees/profile/employee-id``

### Terminate employee

URL: ``app/employees/{employee_id}/terminate`` PATCH request

Data: 

```
{
    description: "Your description"
}
```


### Rejoin the employee

URL: ``app/employees/{employee_id}/rejoin`` PATCH request

```
{
    description: "Your description",
    employment_status_id: 'required'
}
```

### Change termination reason api

URL: ```app/employees/{employee_id}/update-termination-note``` PATCH request

Data: 
```
{
   description: "Termination reason",  
}
```

### Add to employee API

URL: ```users/{user_id}/add-to-employee```

### Remove from employee API

URL: ```users/{user_id}/remomve-from-employee```

### Cancel employee invitation

URL: app/employees/{employee_id}/cancel-invitation DELETE Request


### Employee address 

Get addresses: ```app/employees/{employee_id}/addresses``` GET Request

Update address: ```app/employees/{employee_id}/addresses``` Patch request

Data: 
```
{
    'details': '',
    'area': '', 
    'city': '', 
    'type': ''//present_address or permanent_address,
    'state': '',
    'zip_code': '',
    'country': '',
    'phone_number: ''
}
```

Delete Address: ```app/employees/{employee_id}/addresses/{type} //present_address or permanent_address```

### Employee emergency contacts

API Resource: ```app/employees/{employee}/emergency-contacts```

Data: 
```
{
    'name', 
    'relationship',
    'phone_number',
    'email',
    'details', 
    'city', 
    'country'
}
```