import React, {Component} from 'react';

/**
 * Register|Singleton
 */
class BackToFrontMediator extends Component
{
    constructor(registerID)
    {
        super();
        this.register = $(registerID);
    }

    getRegisterData(name)
    {
        return this.register.data(name);
    }
}

export default BackToFrontMediator