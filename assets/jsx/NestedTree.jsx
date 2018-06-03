/*Start region Libraries*/
import React, {Component} from 'react';
import PropTypes from  'prop-types';
import ReactDOM from 'react-dom';
/*End region Libraries*/

/*Start region Components*/
/*End region Components*/

{debugger}

/**
 *
 */
class NestedTree extends Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            data: {},
        };

        this.setRootUrl();
        this.getActionsUrl();
    }

    setRootUrl()
    {
        this.props.urls.root = document.location.origin + this.props.controllerUrl;
        this.props.urls.actions = this.props.urls.root + this.props.getActionsUrl;
    }

    getActionsUrl()
    {
        $.ajax({
            url: this.props.urls.actions,
            type: 'POST',
            data: {} ,
            dataType:'json',
            beforeSend : function() {},
            success: (data) => {
                this.setActionsUrl(data);
                this.getLevelComments(2);
            },
            error:function(data) {}
        });
    }

    /**
     * @param data
     */
    setActionsUrl(urls)
    {
        this.props.urls.addNode = this.props.urls.root + urls.addNodeAction;
        this.props.urls.deleteNode = this.props.urls.root + urls.deleteNodeAction;
        this.props.urls.getLevel = this.props.urls.root + urls.getLevelAction;
        this.props.urls.getSubTree = this.props.urls.root + urls.getSubTreeAction;
        this.props.urls.index = this.props.urls.root + urls.indexAction;
    }

    getLevelComments(level)
    {
        $.ajax({
              url: this.props.urls.getLevel,
              type: 'POST',
              data: {
                  level:level
              } ,
              dataType:'json',
              beforeSend : function() {},
              success: (data) => {
                  this.state.data = data;
                  this.buildTree();
              },
              error:function(data) {}
        });
    }

    buildTree()
    {
        var html= '<ul>';

        this.state.data.map((key,idx) => {
            html+= '<li>'
            this.buildNode(level);
            html+= '</li>'
        })

        html+= '</ul>';
    }

    buildNode(level, right_key, left_key)
    {

    }

    render(){
        return(
            <div>123</div>
        );
    }
}

NestedTree.propTypes = {
    urls : PropTypes.shape({
        root : PropTypes.string.isRequired,
        index : PropTypes.string.isRequired,
        actions : PropTypes.string.isRequired,
        addNode : PropTypes.string.isRequired,
        deleteNode : PropTypes.string.isRequired,
        getLevel : PropTypes.string.isRequired,
        getSubTree : PropTypes.string.isRequired
    }).isRequired,
    getActionsUrl : PropTypes.string.isRequired,
    controllerUrl : PropTypes.string.isRequired
};

NestedTree.defaultProps = {
    urls : {
        root : '',
        index : '',
        actions : '',
        addNode : '',
        deleteNode : '',
        getLevel : '',
        getSubTree : ''
    }
}

ReactDOM.render(
    <div>
        <h1>
            Nested Tree Comments
        </h1>
        <NestedTree getActionsUrl={'/getActions'} controllerUrl={'/comments'} />
    </div>,
    document.getElementById('nestedTree')
);

export default NestedTree
