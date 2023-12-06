import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import classnames from 'classnames';
import Animate from 'rc-animate';
import Loader from '../Loader';
import styles from './Card.less';

export default class Card extends PureComponent {

  render() {
    const {
      className, children, inner = false, loading,
      fixHeight = false, noPadding = false, noMinHeight = false, style } = this.props;
    const loadingStyle = {
      // height: 'calc(100vh - 184px)',
      overflow: 'hidden',
    };
    return (
      <div className={styles.pageRoot} >
        <div
          className={classnames(className,
            {
              [styles.contentInner]: inner,
              [styles.contentFixHeight]: fixHeight,
              [styles.contentNoPadding]: noPadding,
              [styles.contentNoMinHeight]: noMinHeight,
            })}
          style={loading ? loadingStyle : style}
        >
          {loading ? <Loader spinning /> : ''}
          {!loading && (
            <Animate transitionAppear transitionName="fade">
              {children}
            </Animate>
          )}
        </div>
      </div>
    );
  }
}

Card.propTypes = {
  className: PropTypes.string,
  children: PropTypes.node,
  loading: PropTypes.bool,
  inner: PropTypes.bool,
};
