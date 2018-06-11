import React, {Component} from 'react';

class TreeNode extends Component
{
    constructor(props){
        super(props);

        this.state = {
            newMessage: '',
            edit:{commentID : this.props.comment_editID},
            addNew:{commentID:null}
        };

        this.changedMessage = this.props.comment;

    }

    changeMessageText(e){
        this.changedMessage = e.target.value;
    }

    newMessageText(e){
        //todo shouldComponentUpdate
        this.setState({
            newMessage : e.target.value
        });
    }

    buttonMessageSave(){
        return(
            <button onClick={this.updateMessageBody.bind(this)}>save</button>
        )
    }

    buttonEditCancel(){
        return(
            <button onClick={this.cancelEdit.bind(this)}>cancel</button>
        )
    }

    buttonSendCancel(){
        return(
            <button onClick={this.cancelSend.bind(this)}>cancel</button>
        )
    }

    addComment(){
        let message = this.state.newMessage;
        this.setState({
            newMessage : '',
            addNew : {commentID:null}
        });
        this.props.onAddNode(this.props, message);
    }

    buttonSendMessage(){
        return(
            <button onClick={this.addComment.bind(this)}>send</button>
        )
    }

    textAreaEdit() {
        let id = this.props.comment_id,
            container = $(".MessageBody[data-id="+id+"]");

        return(
            <textarea
                style={{width:container.width(),height:container.height()}}
                defaultValue={this.props.comment}
                onChange={this.changeMessageText.bind(this)}
            ></textarea>
        );
    }

    textAreaNewMessage(){
        return(
            <textarea
                className="NewMessageArea"
                defaultValue={this.state.newMessage}
                onChange={this.newMessageText.bind(this)}
            ></textarea>
        );
    }

    messageContainer() {
        return (
            <div onDoubleClick={this.editNode.bind(this,this.props.comment_id)}>
                {this.changedMessage}
            </div>
        )
    }

    getMessageBody() {
        if( this.state.edit.commentID === this.props.comment_id )
        {
            return (
                <div>
                    {this.buttonMessageSave()}
                    {this.buttonEditCancel()}
                    {this.textAreaEdit()}
                </div>
            );
        }
        else if(this.state.addNew.commentID === this.props.comment_id ){
            return(
                <div>
                    {this.messageContainer()}
                    <div className="subMessageContainer">
                        {this.textAreaNewMessage()}
                        <div style={{float:"right",marginBottom:"15px"}}>
                            {this.buttonSendCancel()}
                            {this.buttonSendMessage()}
                        </div>
                    </div>
                </div>
            )
        }
        else{
            return (
                this.messageContainer()
            )
        }
    }

    updateMessageBody(){

        let changedMessage = this.changedMessage,
            commentID = this.props.comment_id,
            url = this.props.editUrl;

        $.ajax({
              url: url,
              type: 'POST',
              data: {
                  comment_id : commentID,
                  comment : changedMessage
              } ,
              dataType:'html',
              beforeSend : function() {
                  //todo gif
              },
              success: (data) => {
                  this.setState({
                      edit: {
                          commentID : null
                      },
                      data:{
                          comment: changedMessage
                      }
                  })
              },
              error:(data) => {
                  //todo translates?
                  console.log(data);
              }
        });

    }

    cancelSend(){
        this.setState({
            addNew: {
                commentID : null
            }
        });
    }

    cancelEdit(){
        this.setState({
            edit: {
                commentID : null
            }
        });
    }

    editNode(commentID) {
        this.setState({edit: {
                commentID : commentID
        }});
    }

    addNode() {
        this.setState({
            addNew: {
                commentID : this.props.comment_id
            },
            edit: {
                commentID : null
            }
        })
    }

    shouldComponentUpdate(newProps, newState) {
        return true
    }

    render() {
        return (

            <div className="Comment">
                <div className="actions">

                    <button onClick={this.props.onFadeOut.bind(
                        null,
                        this.props.comment_id,
                        this.props.left_key,
                        this.props.right_key
                    )}>+</button>

                    comment_id : {this.props.comment_id}
                    parent_id : {this.props.parent_id}

                    <button className="ButtonDelete"
                            onClick={this.props.onDeleteNode.bind(null,
                                this.props.comment_id,
                                this.props.left_key,
                                this.props.right_key
                            )}
                    >Delete</button>

                    <button className="ButtonAddComment"
                            onClick={this.addNode.bind(this)}

                    >Add Comment</button>

                </div>


                <div data-id={this.props.comment_id}
                     className="MessageBody">{this.getMessageBody(null)}
                </div>

            </div>

        );
    }
}

export default TreeNode