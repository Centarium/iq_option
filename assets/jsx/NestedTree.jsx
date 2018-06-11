/*Start region Libraries*/
import React, {Component} from 'react';
import PropTypes from  'prop-types';
import ReactDOM from 'react-dom';
/*End region Libraries*/

/*Start region Components*/
import TreeNode from './Components/TreeNode';
/*End region Components*/

{debugger}

class NestedTree extends Component
{
    constructor(props)
    {
        super(props);

        this.defaultLevel = 1;

        this.state = {
            data: [],
            edit:{commentID : null}
        };

        this.setRootUrl();
        this.initialData();
    }

    setRootUrl()
    {
        this.props.urls.root = document.location.origin + this.props.controllerUrl;
        this.props.urls.actions = this.props.urls.root + this.props.getActionsUrl;
    }

    initialData()
    {
        $.ajax({
            url: this.props.urls.actions,
            type: 'POST',
            data: {} ,
            dataType:'json',
            beforeSend : function() {},
            success: (data) => {
                this.setActionsUrl(data);

                this.setLevelComments(this.defaultLevel)
            },
            error:function(data) {}
        });
    }

    /**
     * todo one switch - case instead one action - one method
     * @param urls
     */
    setActionsUrl(urls)
    {
        this.props.urls.addNode = this.props.urls.root + urls.addNodeAction;
        this.props.urls.deleteNode = this.props.urls.root + urls.deleteNodeAction;
        this.props.urls.editNode = this.props.urls.root + urls.editNodeAction;
        this.props.urls.getLevel = this.props.urls.root + urls.getLevelAction;
        this.props.urls.getSubTree = this.props.urls.root + urls.getSubTreeAction;
        this.props.urls.index = this.props.urls.root + urls.indexAction;
    }

    setLevelComments(level)
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
                  this.setState({data: data});
              },
              error:function(data) {
                  console.log(data.responseText)
              }
        });
    }

    /**
     * @param comment_id
     * @param left_key
     * @param right_key
     */
    getSubTree(comment_id, left_key, right_key)
    {
        $.ajax({
            url: this.props.urls.getSubTree,
            type: 'POST',
            data: {
                right_key:right_key,
                left_key:left_key,
                comment_id:comment_id
            } ,
            dataType:'json',
            beforeSend : function() {},
            success: (data) => {
                this.expandNodes(data)
            },
            error:function(data) {}
        });
    }

    expandNodes(data)
    {
        let node = data.comment_id;

        for( let item in this.state.data )
        {
            if( node == item )
            {
                for(let i in data.tree)
                {
                    this.state.data[item].childs[i] = data.tree[i];
                }
            }
        }

        this.setState({});
    }

    buildTree(data)
    {
        let array = [];

        if( !(data instanceof Array))
        {
            for(let item in data)
            {
                array[item] = data[item];
            }
        }
        else array = data;

        if( array.length == 0 ) return ;

        return(
            <ul>
                {this.buildLevel(array)}
            </ul>
        );
    }

    saveNode(data, newMessage)
    {
        $.ajax({
            url: this.props.urls.addNode,
            type: 'POST',
            data: {
                right_key: data.right_key,
                comment_id: data.comment_id,
                comment: newMessage,
                level : data.level
            } ,
            dataType:'json',
            beforeSend : function() {
                //todo gif
            },
            success: (data) => {
                this.setLevelComments(this.defaultLevel);
            },
            error:function(data) {
                console.log(data);
            }
        });
    }

    createFirstLevelNode(e){

        let newMessage = this.refs.newMessage.value;
        if( newMessage === '' ) return false;

        this.refs.newMessage.value = '';
        let data = {
            right_key : 0,
            comment_id : 0,
            level : 0,
            comment : ''
        };
        this.saveNode(data,newMessage);
    }

    newMessageContainer(){
        return(
            <div className="newMessageContainer">
                    <textarea ref="newMessage" name="newMessage" className="NewMessageArea"></textarea>
                    <button onClick={this.createFirstLevelNode.bind(this)} >sendMessage</button>
            </div>
        )
    }

    deleteNode(comment_id, left_key, right_key)
    {
        $.ajax({
            url: this.props.urls.deleteNode,
            type: 'POST',
            data: {
                right_key:right_key,
                left_key:left_key,
                comment_id:comment_id
            } ,
            dataType:'json',
            beforeSend : function() {
                //todo gif
            },
            success: (data) => {
                this.setLevelComments(this.defaultLevel);
            },
            error:function(data) {
                console.log(data);
            }
        });
    }

    buildLevel(data)
    {
        return data.map((object,idx) => {
            return(
                <li key={object.value.comment_id} >

                    <TreeNode
                        comment_editID={this.state.edit.commentID}
                        comment_id = {object.value.comment_id}
                        left_key={object.value.left_key}
                        right_key={object.value.right_key}
                        comment={object.value.comment}
                        level={object.value.level}
                        parent_id ={object.value.parent_id}

                        editUrl={this.props.urls.editNode}

                        onFadeOut={this.getSubTree.bind(this)}
                        onDeleteNode={this.deleteNode.bind(this)}
                        onAddNode={this.saveNode.bind(this)}
                    />

                    {this.buildTree(object.childs)}

                </li>
            )
        },this)
    }

    componentWillUpdate()
    {
        this.start = new Date().getMilliseconds();
    }

    componentDidUpdate()
    {
        console.log('Время выполнения ' + (new Date().getMilliseconds() - this.start) + ' msec' )
    }

    render(){
        return(
            <div>
                {this.newMessageContainer()}
                {this.buildTree(this.state.data)}
            </div>
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
