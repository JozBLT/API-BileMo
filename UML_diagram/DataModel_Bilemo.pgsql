
        +-----------------+
        |    Client       |
        +-----------------+
        | id (PK)         |
        | name (unique)   |
        | createdAt       |
        | updatedAt       |
        +-----------------+
                |
                | 1
                |
                |*
        +-----------------+
        |    User         |
        +-----------------+
        | id (PK)         |
        | email (unique)  |
        | roles (json)    |
        | password        |
        | firstname       |
        | lastname        |
        | client_id (FK)  |
        | createdAt       |
        | updatedAt       |
        +-----------------+

        +-----------------+
        |   Product       |
        +-----------------+
        | id (PK)         |
        | name            |
        | brand           |
        | description     |
        | price           |
        | createdAt       |
        | updatedAt       |
        +-----------------+
