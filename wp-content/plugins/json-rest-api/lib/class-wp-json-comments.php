<?php

class WP_JSON_Comments {
	/**
	 * Base route name.
	 *
	 * @var string Route base (e.g. /my-plugin/my-type/(?P<id>\d+)/meta). Must include ID selector.
	 */
	protected $base = '/posts/(?P<id>\d+)/comments';

	/**
	 * Register the comment-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes( $routes ) {
		$routes[ $this->base ] = array(
			array( array( $this, 'get_comments' ),   WP_JSON_Server::READABLE ),
		);
		$routes[ $this->base . '/(?P<comment>\d+)'] = array(
			array( array( $this, 'get_comment' ),    WP_JSON_Server::READABLE ),
			array( array( $this, 'delete_comment' ), WP_JSON_Server::DELETABLE ),
		);
                
                $routes[ $this->base.'/?add'] = array(
                        array( array( $this, 'add_comment' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON )
                );

		return $routes;
	}

	/**
	 * Delete a comment.
	 *
	 * @uses wp_delete_comment
	 * @param int $id Post ID
	 * @param int $comment Comment ID
	 * @param boolean $force Skip trash
	 * @return array
	 */
	public function delete_comment( $id, $comment, $force = false ) {
		$comment = (int) $comment;

		if ( empty( $comment ) ) {
			return new WP_Error( 'json_comment_invalid_id', __( 'Invalid comment ID.' ), array( 'status' => 404 ) );
		}

		$comment_array = get_comment( $comment, ARRAY_A );

		if ( empty( $comment_array ) ) {
			return new WP_Error( 'json_comment_invalid_id', __( 'Invalid comment ID.' ), array( 'status' => 404 ) );
		}

		if ( ! current_user_can(  'edit_comment', $comment_array['comment_ID'] ) ) {
			return new WP_Error( 'json_user_cannot_delete_comment', __( 'Sorry, you are not allowed to delete this comment.' ), array( 'status' => 401 ) );
		}

		$result = wp_delete_comment( $comment_array['comment_ID'], $force );

		if ( ! $result ) {
			return new WP_Error( 'json_cannot_delete', __( 'The comment cannot be deleted.' ), array( 'status' => 500 ) );
		}

		if ( $force ) {
			return array( 'message' => __( 'Permanently deleted comment' ) );
		} else {
			// TODO: return a HTTP 202 here instead
			return array( 'message' => __( 'Deleted comment' ) );
		}
	}

	/**
	 * Retrieve comments
	 *
	 * @param int $id Post ID to retrieve comments for
	 * @return array List of Comment entities
	 */
	public function get_comments( $id ) {

		$args = array(
                    'post_id' => $id, 
                    'offset' => isset($_GET['offset'])?$_GET['offset']:"" , 
                    'number' => isset($_GET['number'])?$_GET['number']:"" ,
                    'parent' => isset($_GET['parent'])?$_GET['parent']:"" 
                );
		$comments = get_comments( $args );

		$post = get_post( $id, ARRAY_A );

		if ( empty( $post['ID'] ) ) {
			return new WP_Error( 'json_post_invalid_id', __( 'Invalid post ID.' ), array( 'status' => 404 ) );
		}

		if ( ! json_check_post_permission( $post, 'read' ) ) {
			return new WP_Error( 'json_user_cannot_read', __( 'Sorry, you cannot read this post.' ), array( 'status' => 401 ) );
		}
                
                
                foreach($comments as $comment){
                    $comment->has_child = (get_comments( array('parent'=>$comment->comment_ID)))?true:false;
                }
		$struct = array();

		foreach ( $comments as $comment ) {
                    
			$struct[] = $this->prepare_comment( $comment, array( 'comment', 'meta' ), 'collection' );
		}
		return $struct;
	}

	/**
	 * Retrieve a single comment
	 *
	 * @param int $comment Comment ID
	 * @return array Comment entity
	 */
	public function get_comment( $comment ) {
		$comment = get_comment( $comment );

		if ( empty( $comment ) ) {
			return new WP_Error( 'json_comment_invalid_id', __( 'Invalid comment ID.' ), array( 'status' => 404 ) );
		}

		$data = $this->prepare_comment( $comment );

		return $data;
	}

	/**
	 * Prepares comment data for returning as a JSON response.
	 *
	 * @param stdClass $comment Comment object
	 * @param array $requested_fields Fields to retrieve from the comment
	 * @param string $context Where is the comment being loaded?
	 * @return array Comment data for JSON serialization
	 */
	protected function prepare_comment( $comment, $requested_fields = array( 'comment', 'meta' ), $context = 'single' ) {
		$fields = array(
			'ID'   => (int) $comment->comment_ID,
			'post' => (int) $comment->comment_post_ID,
                        'has_child' =>  $comment->has_child
		);
                
		$post = (array) get_post( $fields['post'] );

		// Content
		$fields['content'] = apply_filters( 'comment_text', $comment->comment_content, $comment );
		// $fields['content_raw'] = $comment->comment_content;

		// Status
		switch ( $comment->comment_approved ) {
			case 'hold':
			case '0':
				$fields['status'] = 'hold';
				break;

			case 'approve':
			case '1':
				$fields['status'] = 'approved';
				break;

			case 'spam':
			case 'trash':
			default:
				$fields['status'] = $comment->comment_approved;
				break;
		}

		// Type
		$fields['type'] = apply_filters( 'get_comment_type', $comment->comment_type );

		if ( empty( $fields['type'] ) ) {
			$fields['type'] = 'comment';
		}

		// Parent
		if ( ( 'single' === $context || 'single-parent' === $context ) && (int) $comment->comment_parent ) {
			$parent_fields = array( 'meta' );

			if ( $context === 'single' ) {
				$parent_fields[] = 'comment';
			}
			$parent = get_comment( $comment->comment_parent );

			$fields['parent'] = $this->prepare_comment( $parent, $parent_fields, 'single-parent' );
		}

		// Parent
		$fields['parent'] = (int) $comment->comment_parent;

		// Author
		if ( (int) $comment->user_id !== 0 ) {
			$fields['author'] = (int) $comment->user_id;
		} else {
			$fields['author'] = array(
				'ID'     => 0,
				'name'   => $comment->comment_author,
				'URL'    => $comment->comment_author_url,
				'avatar' => json_get_avatar_url( $comment->comment_author_email ),
			);
		}

		// Date
		$timezone     = json_get_timezone();
		$comment_date = WP_JSON_DateTime::createFromFormat( 'Y-m-d H:i:s', $comment->comment_date, $timezone );

		$fields['date']     = json_mysql_to_rfc3339( $comment->comment_date );
		$fields['date_tz']  = $comment_date->format( 'e' );
		$fields['date_gmt'] = json_mysql_to_rfc3339( $comment->comment_date_gmt );

		// Meta
		$meta = array(
			'links' => array(
				'up' => json_url( sprintf( '/posts/%d', (int) $comment->comment_post_ID ) )
			),
		);

		if ( 0 !== (int) $comment->comment_parent ) {
			$meta['links']['in-reply-to'] = json_url( sprintf( '/posts/%d/comments/%d', (int) $comment->comment_post_ID, (int) $comment->comment_parent ) );
		}

		if ( 'single' !== $context ) {
			$meta['links']['self'] = json_url( sprintf( '/posts/%d/comments/%d', (int) $comment->comment_post_ID, (int) $comment->comment_ID ) );
		}

		// Remove unneeded fields
		$data = array();

		if ( in_array( 'comment', $requested_fields ) ) {
			$data = array_merge( $data, $fields );
		}

		if ( in_array( 'meta', $requested_fields ) ) {
			$data['meta'] = $meta;
		}

		return apply_filters( 'json_prepare_comment', $data, $comment, $context );
	}

	/**
	 * Call protected method from {@see WP_JSON_Posts}.
	 *
	 * WPAPI-1.2 deprecated a bunch of protected methods by moving them to this
	 * class. This proxy method is added to call those methods.
	 *
	 * @param string $method Method name
	 * @param array $args Method arguments
	 * @return mixed Return value from the method
	 */
	public function _deprecated_call( $method, $args ) {
		return call_user_func_array( array( $this, $method ), $args );
	}
        
        public function add_comment($id, $data){
            $id = (int) $id;
            $commentdata = array();

                if(empty($id)){
                    return new WP_Error( "json_post_invalid_id", __( "Invalid post ID." ), array( "status" => 404 ) );
                }
                $post = get_post( $id, ARRAY_A );
                if(empty($post["ID"])){
                    return new WP_Error( "json_post_invalid_id", __( "Invalid post ID." ), array( "status" => 404 ) );
                }

                $commentdata["comment_post_ID"] = $id;

                if(!comments_open($id)){
                    return new WP_Error( "json_comment_restricted", __( "Comment not allowed here." ), array( "status" => 403 ) );
                }

                if(!empty( $data["user_id"])){
                    //maybe check user_id against whether user must be logged in...  Don"t think being done below
                    $commentdata["user_id"] = $data["user_id"];
                }
                if(!empty( $data["comment_author_email"])){
                    //maybe check if user_id corresponds with email
                    $commentdata["comment_author_email"] = $data["comment_author_email"];
                }
                if(!empty( $data["comment_author"])){
                    //maybe check if user_id corresponds with author
                    $commentdata["comment_author"] = $data["comment_author"];
                }
                if(!empty( $data["author_website"])){$commentdata["comment_author_url"] = $data["author_website"];}

                if(!empty( $data["comment_content"])){
                    if(empty($data["comment_content"]))
                        return new WP_Error( "json_empty_value", __( "No content" ), array( "status" => 400 ) );
                    $commentdata["comment_content"] = $data["comment_content"];
                }
                if(!empty( $data["type"])){
                    //future support for custom comment types
                    if($data["type"] != "comment" || $data["type"] != "trackback" || $data["type"] != "pingback")
                        return new WP_Error( "json_invalid_comment_type", __( "Invalid comment type" ), array( "status" => 400 ) );
                    $commentdata["comment_type"] = $data["type"];
                }

                if(!empty( $data["parent"])){$commentdata["comment_parent"] = $data["parent"];}

                if(!empty( $data["date"])){$commentdata["comment_date"] = $data["date"];}

                if(!empty( $data["approved"])){$commentdata["comment_approved"] = $data["approved"];}

                /* Should we handle comment_author_IP and or comment_agent */       


                // From wp_new_comment()

                $commentdata = apply_filters( "preprocess_comment", $commentdata, $data);

                //$commentdata["comment_post_ID"] = (int) $commentdata["comment_post_ID"];

                if(isset($commentdata["user_id"]))$commentdata["user_id"] = (int) $commentdata["user_id"];

                $commentdata["comment_parent"] = isset($commentdata["comment_parent"]) ? absint($commentdata["comment_parent"]) : 0;
                $parent_status = ( 0 < $commentdata["comment_parent"] ) ? wp_get_comment_status($commentdata["comment_parent"]) : "";
                $commentdata["comment_parent"] = ( "approved" == $parent_status || "unapproved" == $parent_status ) ? $commentdata["comment_parent"] : 0;

                //Do we want these
                $commentdata["comment_author_IP"] = preg_replace( "/[^0-9a-fA-F:., ]/", "",$_SERVER["REMOTE_ADDR"] );
                $commentdata["comment_agent"]     = isset( $_SERVER["HTTP_USER_AGENT"] ) ? substr( $_SERVER["HTTP_USER_AGENT"], 0, 254 ) : "";

                //Allow for date to be set manually?
                $commentdata["comment_date"]     = isset($commentdata["comment_date"])?$commentdata["comment_date"]:current_time("mysql");
                $commentdata["comment_date_gmt"] = isset($commentdata["comment_date_gmt"])?$commentdata["comment_date_gmt"]:current_time("mysql", 1);

                $commentdata = wp_filter_comment($commentdata);
                $commentdata["comment_approved"] = wp_allow_comment($commentdata);
                $comment_ID = wp_insert_comment($commentdata);

                /**
                 * Fires immediately after a comment is inserted into the database.
                 *
                 * @since 1.2.0
                 *
                 * @param int $comment_ID       The comment ID.
                 * @param int $comment_approved 1 (true) if the comment is approved, 0 (false) if not.
                 */
                do_action( "comment_post", $comment_ID, $commentdata["comment_approved"] );

                if ( "spam" !== $commentdata["comment_approved"] ) { // If it"s spam save it silently for later crunching
                        if ( "0" == $commentdata["comment_approved"] ) {
                                wp_notify_moderator( $comment_ID );
                        }

                        // wp_notify_postauthor() checks if notifying the author of their own comment.
                        // By default, it won"t, but filters can override this.
                        if ( get_option( "comments_notify" ) && $commentdata["comment_approved"] ) {
                                wp_notify_postauthor( $comment_ID );
                        }
                }

                //return whatever is necessary, maybe all comments relating to post?
                return $this->get_comment($comment_ID);
        }
}
