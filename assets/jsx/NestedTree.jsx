/*Start region Libraries*/
import React, {Component} from 'react';
import PropTypes from  'prop-types';
import ReactDOM from 'react-dom';
/*End region Libraries*/

/*Start region Components*/
import BackToFrontMediator from './Components/BackToFrontMediator';
/*End region Components*/

const BTF = new BackToFrontMediator('#nestedTree');

class NestedTree extends Component
{
    constructor()
    {
        super();

        this.state = {
            data: {},
        };
    }

    getLevelComments(level)
    {
        console.log('1');
    }

    render(){
        return(
            <div className="Tree">{this.getLevelComments(1)}</div>
        );
    }
}

/*NestedTree.propTypes = {
    schema: PropTypes.objectOf(
        PropTypes.object
    ),
    initialData: PropTypes.arrayOf(
        PropTypes.object
    ),
    onDataChange: PropTypes.func
};*/

NestedTree.defaultProps = {
    props : {
        addNodeAction : BTF.getRegisterData('add_node_action'),
        //addNodeAction : $('#nestedTree').data('add_node_action'),
        //indexAction : $('#nestedTree').data('get_tree_action')
        //indexAction : $('#nestedTree').data('get_tree_action')
        //indexAction : $('#nestedTree').data('get_tree_action')
        //indexAction : $('#nestedTree').data('get_tree_action')
    }
}

ReactDOM.render(
    <div>
        <h1>
            Nested Tree Comments
        </h1>
        <NestedTree />
    </div>,
    document.getElementById('nestedTree')
);

export default NestedTree
